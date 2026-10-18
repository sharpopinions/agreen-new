<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\CustomerDocument;
use App\Models\Language;
use App\Models\Lead;
use App\Models\Order;
use App\Models\PaymentProvider;
use App\Models\Product;
use App\Models\Service;
use App\Models\ShippingProvider;
use App\Models\User;
use App\Notifications\NewLead;
use App\Services\Cart;
use App\Services\Cashback;
use App\Support\Seo;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Особистий кабінет клієнта (Claude Design → client-cabinet.jsx).
 * Кожен розділ — окрема адреса /account/{розділ}, сторінки — Pages/Account/*.vue.
 */
class AccountController extends Controller
{
    /** Типи звернень у техпідтримку: ключ => назва (зберігається в заявці). */
    public const SUPPORT_KINDS = [
        'consultation' => 'Консультація',
        'service'      => 'Сервіс',
        'specialist'   => 'Виклик спеціаліста',
    ];

    public function index(): Response
    {
        return $this->render('Account/Catalog', 'Каталог', [
            'categories' => HomeController::rootCategories(Language::currentId()),
        ]);
    }

    public function orders(Request $request): Response
    {
        $orders = $request->user()->orders()
            ->with(['items.product.translations', 'status.translations', 'shippingProvider.translations', 'paymentProvider.translations'])
            ->latest('id')
            ->paginate(10);

        $invoices = CustomerDocument::where('user_id', $request->user()->id)
            ->whereIn('order_id', $orders->pluck('id'))
            ->where('type', 'invoice')
            ->pluck('id', 'order_id');

        return $this->render('Account/Orders', 'Замовлення', [
            'orders' => $orders->through(fn(Order $o) => [
                'id'       => $o->id,
                'number'   => $o->number,
                'date'     => $o->created_at->translatedFormat('j M Y'),
                'type'     => $o->type,
                'status'   => ['key' => $o->status?->key, 'name' => $o->status?->name, 'color' => $o->status?->color],
                'total'    => (float) $o->total,
                'count'    => (int) $o->items->sum('quantity'),
                'delivery' => $o->shippingProvider?->name,
                'city'     => $o->delivery_city,
                'address'  => $o->delivery_address,
                'tracking' => $o->tracking_number,
                'payment'  => $o->paymentProvider?->name,
                'invoice'  => isset($invoices[$o->id]) ? route('account.documents.download', $invoices[$o->id]) : null,
                'items'    => $o->items->map(fn($i) => [
                    'name'     => $i->name,
                    'sku'      => $i->sku,
                    'price'    => (float) $i->price,
                    'quantity' => $i->quantity,
                    'total'    => (float) $i->total,
                    'slug'     => $i->product?->is_active ? $i->product->translations->firstWhere('language_id', Language::currentId())?->slug : null,
                ]),
            ]),
        ]);
    }

    /** Повторити замовлення: доступні товари — у кошик. */
    public function repeat(Request $request, Order $order, Cart $cart): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $products = Product::with('stockStatus')->where('is_active', true)
            ->whereIn('id', $order->items()->pluck('product_id')->filter())
            ->get()->keyBy('id');

        $skipped = 0;
        foreach ($order->items as $item) {
            $product = $products->get($item->product_id);
            $product ? $cart->add($product, $item->quantity) : $skipped++;
        }

        return redirect()->route('cart')->with('notice', $skipped
            ? "Товари додано до кошика. {$skipped} поз. більше немає в продажу."
            : 'Товари із замовлення #' . $order->number . ' додано до кошика.');
    }

    public function documents(Request $request): Response
    {
        $type = in_array($request->query('type'), CustomerDocument::TYPES, true) ? $request->query('type') : null;

        $docs = $request->user()->documents()
            ->when($type, fn($q) => $q->where('type', $type))
            ->orderByDesc('issued_at')->orderByDesc('id')
            ->get()
            ->map(fn(CustomerDocument $d) => [
                'id'    => $d->id,
                'title' => $d->title,
                'type'  => $d->type,
                'date'  => ($d->issued_at ?? $d->created_at)->translatedFormat('j M Y'),
                'ext'   => $d->extension(),
                'size'  => $d->size,
                'url'   => route('account.documents.download', $d),
            ]);

        return $this->render('Account/Documents', 'Документообіг', [
            'documents' => $docs,
            'type'      => $type,
        ]);
    }

    public function download(Request $request, CustomerDocument $document): StreamedResponse
    {
        abort_unless($document->user_id === $request->user()->id, 404);
        abort_unless(Storage::disk(CustomerDocument::DISK)->exists($document->path), 404);

        $name = $document->title . '.' . strtolower($document->extension());

        return Storage::disk(CustomerDocument::DISK)->download($document->path, $name);
    }

    public function terms(Request $request, Cashback $cashback): Response
    {
        $user = $request->user();

        return $this->render('Account/Terms', 'Умови співпраці', [
            'cashback' => $cashback->summary($user),
            'levels'   => $cashback->levels(),
            'history'  => $user->cashbackTransactions()->latest()->latest('id')->limit(30)->get()->map(fn($t) => [
                'id'     => $t->id,
                'amount' => (float) $t->amount,
                'text'   => $t->description,
                'date'   => $t->created_at->translatedFormat('j M Y'),
            ]),
        ]);
    }

    /** Навчання: курси навчального центру + найближчі події з блогу. */
    public function study(): Response
    {
        $langId = Language::currentId();

        $courses = Service::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->where('is_active', true)->orderBy('sort_order')->get()
            ->flatMap(function (Service $s) {
                $t = $s->translations->first();

                return collect($t?->courses ?? [])->map(fn($c) => [
                    'title'    => $c['title'] ?? '',
                    'duration' => $c['duration'] ?? null,
                    'level'    => $c['level'] ?? null,
                    'price'    => $c['price'] ?? null,
                    'service'  => $t?->title,
                    'slug'     => $t?->slug,
                ]);
            })->values();

        $events = BlogController::query()
            ->where('type', 'event')
            ->where('event_starts_at', '>=', now()->startOfDay())
            ->reorder('event_starts_at')
            ->limit(6)->get()
            ->map(fn(BlogPost $p) => BlogController::card($p) + [
                'starts' => $p->event_starts_at?->translatedFormat('j M Y, H:i'),
            ]);

        return $this->render('Account/Study', 'Навчання', compact('courses', 'events'));
    }

    public function support(Request $request): Response
    {
        $statuses = ['new' => 'Відкритий', 'in_progress' => 'В обробці', 'done' => 'Вирішений', 'spam' => 'Закритий'];

        return $this->render('Account/Support', 'Техпідтримка', [
            'kinds'    => collect(self::SUPPORT_KINDS)->map(fn($label, $key) => ['key' => $key, 'label' => $label])->values(),
            'requests' => $request->user()->supportRequests()->latest('id')->get()->map(fn(Lead $l) => [
                'id'      => $l->id,
                'number'  => sprintf('T-%04d', $l->id),
                'kind'    => $l->payload['request'] ?? '',
                'message' => $l->message,
                'status'  => $l->status,
                'label'   => $statuses[$l->status] ?? $l->status,
                'date'    => $l->created_at->translatedFormat('j M Y'),
            ]),
        ]);
    }

    public function storeSupport(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kind'    => ['required', Rule::in(array_keys(self::SUPPORT_KINDS))],
            'message' => ['required', 'string', 'max:3000'],
        ], [], ['kind' => 'тип звернення', 'message' => 'опис']);

        $user = $request->user();
        $lead = Lead::create([
            'type'       => 'support',
            'user_id'    => $user->id,
            'name'       => $user->fullName(),
            'phone'      => $user->phone,
            'email'      => $user->email,
            'company'    => $user->company,
            'message'    => $data['message'],
            'payload'    => ['request' => self::SUPPORT_KINDS[$data['kind']]],
            'source_url' => route('account.support'),
            'ip'         => $request->ip(),
        ]);

        if ($to = SiteSettings::all()['emails'][0] ?? null) {
            Notification::route('mail', $to)->notify(new NewLead($lead));
        }

        return back()->with('notice', 'Звернення #' . sprintf('T-%04d', $lead->id) . ' створено. Менеджер звʼяжеться з вами.');
    }

    public function personal(Request $request): Response
    {
        $u = $request->user();

        return $this->render('Account/Personal', 'Персональні дані', [
            'profile' => [
                'name'         => $u->name,
                'last_name'    => (string) $u->last_name,
                'phone'        => (string) $u->phone,
                'email'        => $u->email,
                'company'      => (string) $u->company,
                'company_code' => (string) $u->company_code,
                'delivery'     => $u->default_shipping_provider_id,
                'payment'      => $u->default_payment_provider_id,
                'city'         => (string) $u->default_city,
                'address'      => (string) $u->default_address,
            ],
            'deliveryMethods' => ShippingProvider::available()->map(fn($p) => ['id' => $p->id, 'name' => $p->name]),
            'paymentMethods'  => PaymentProvider::availableFor($u)->map(fn($p) => ['id' => $p->id, 'name' => $p->name]),
        ]);
    }

    public function updatePersonal(Request $request): RedirectResponse
    {
        $user = $request->user();
        $request->merge(['phone' => User::normalizePhone($request->input('phone'))]);

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'last_name'    => ['required', 'string', 'max:100'],
            'phone'        => ['required', 'regex:/^\+\d{11,13}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'company'      => ['nullable', 'string', 'max:255'],
            'company_code' => ['nullable', 'string', 'max:20'],
            'delivery'     => ['nullable', 'integer', Rule::in(ShippingProvider::available()->pluck('id')->all())],
            'payment'      => ['nullable', 'integer', Rule::in(PaymentProvider::availableFor($user)->pluck('id')->all())],
            'city'         => ['nullable', 'string', 'max:255'],
            'address'      => ['nullable', 'string', 'max:255'],
        ], [
            'phone.regex'  => 'Вкажіть телефон у форматі +380 XX XXX XX XX.',
            'phone.unique' => 'Цей номер уже використовується іншим профілем.',
        ], ['name' => 'імʼя', 'last_name' => 'прізвище', 'phone' => 'телефон']);

        $user->update([
            'name'                         => $data['name'],
            'last_name'                    => $data['last_name'],
            'phone'                        => $data['phone'],
            'company'                      => $data['company'] ?? null,
            'company_code'                 => $data['company_code'] ?? null,
            'default_shipping_provider_id' => $data['delivery'] ?? null,
            'default_payment_provider_id'  => $data['payment'] ?? null,
            'default_city'                 => $data['city'] ?? null,
            'default_address'              => $data['address'] ?? null,
        ]);

        return back()->with('notice', 'Зміни збережено.');
    }

    public function settings(Request $request): Response
    {
        return $this->render('Account/Settings', 'Налаштування', [
            'notifications' => $request->user()->notificationSettings(),
        ]);
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $keys = array_keys(User::DEFAULT_NOTIFICATIONS);
        $data = $request->validate(collect($keys)->mapWithKeys(fn($k) => [$k => ['required', 'boolean']])->all());

        $request->user()->update(['notifications' => array_map('boolval', $data)]);

        return back()->with('notice', 'Налаштування сповіщень збережено.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', PasswordRule::min(8)],
        ], [
            'current_password.current_password' => 'Поточний пароль невірний.',
        ], ['current_password' => 'поточний пароль', 'password' => 'новий пароль']);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('notice', 'Пароль змінено.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(
            ['password' => ['required', 'current_password']],
            ['password.current_password' => 'Невірний пароль.'],
            ['password' => 'пароль'],
        );

        $user = $request->user();
        abort_if($user->isStaff(), 403);

        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function render(string $component, string $title, array $props = []): Response
    {
        return Inertia::render($component, $props + [
            'seo' => Seo::make($title . ' — особистий кабінет', noindex: true),
        ]);
    }
}
