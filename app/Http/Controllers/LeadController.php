<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Product;
use App\Notifications\NewLead;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

/**
 * Прийом заявок з форм сайту: POST /forms/{type}.
 * Відповідь — redirect back з flash leadSent (форма показує «Дякуємо…» на місці).
 */
class LeadController extends Controller
{
    private const PHONE = ['regex:/^[0-9+()\-\s]{9,20}$/', 'max:50'];

    public function store(Request $request, string $type): RedirectResponse
    {
        abort_unless(in_array($type, Lead::TYPES, true), 404);

        // Пастка для ботів: приховане поле має лишитися порожнім. Боту — «успіх», у базу нічого
        if (filled($request->input('website'))) {
            return back()->with('leadSent', $type);
        }

        $data = $request->validate($this->rules($type), [
            'phone.regex'    => 'Вкажіть телефон у форматі +380 XX XXX XX XX.',
            'phone.required_without' => 'Вкажіть телефон або email.',
            'email.required_without' => 'Вкажіть телефон або email.',
        ]);

        $payload = collect($data)->only(['quantity', 'partner_type', 'service', 'vacancy'])->filter()->all();

        $lead = Lead::create([
            'type'       => $type,
            'user_id'    => $request->user()?->id,
            'product_id' => $data['product_id'] ?? null,
            'name'       => $data['name'] ?? ($data['email'] ?? $data['phone'] ?? '—'),
            'phone'      => $data['phone'] ?? null,
            'email'      => $data['email'] ?? null,
            'company'    => $data['company'] ?? null,
            'message'    => $data['message'] ?? null,
            'payload'    => $payload ?: null,
            'source_url' => mb_substr((string) $request->headers->get('referer'), 0, 500) ?: null,
            'ip'         => $request->ip(),
        ]);

        $this->notify($lead);

        return back()->with('leadSent', $type);
    }

    private function rules(string $type): array
    {
        $product = ['required', 'integer', Rule::exists(Product::class, 'id')->where('is_active', true)];

        return match ($type) {
            'contact' => [
                'name'    => ['required', 'string', 'max:255'],
                'phone'   => ['required', ...self::PHONE],
                'email'   => ['nullable', 'email', 'max:255'],
                'message' => ['nullable', 'string', 'max:2000'],
            ],
            'quick_order' => [
                'name'       => ['required', 'string', 'max:255'],
                'phone'      => ['required', ...self::PHONE],
                'product_id' => $product,
                'quantity'   => ['nullable', 'integer', 'min:1', 'max:9999'],
            ],
            // Повідомити про надходження: достатньо телефону або email
            'stock_notify' => [
                'name'       => ['nullable', 'string', 'max:255'],
                'phone'      => ['nullable', 'required_without:email', ...self::PHONE],
                'email'      => ['nullable', 'required_without:phone', 'email', 'max:255'],
                'product_id' => $product,
            ],
            'price_request' => [
                'name'       => ['required', 'string', 'max:255'],
                'phone'      => ['required', ...self::PHONE],
                'email'      => ['nullable', 'email', 'max:255'],
                'company'    => ['nullable', 'string', 'max:255'],
                'product_id' => $product,
                'quantity'   => ['nullable', 'integer', 'min:1', 'max:999999'],
                'message'    => ['nullable', 'string', 'max:2000'],
            ],
            'partner' => [
                'company'      => ['required', 'string', 'max:255'],
                'name'         => ['required', 'string', 'max:255'],
                'phone'        => ['required', ...self::PHONE],
                'email'        => ['required', 'email', 'max:255'],
                'partner_type' => ['nullable', Rule::in(['dealer', 'wholesale', 'installer'])],
                'message'      => ['nullable', 'string', 'max:2000'],
            ],
            'service' => [
                'name'    => ['required', 'string', 'max:255'],
                'phone'   => ['required', ...self::PHONE],
                'email'   => ['nullable', 'email', 'max:255'],
                'company' => ['nullable', 'string', 'max:255'],
                'service' => ['nullable', 'string', 'max:255'],
                'message' => ['nullable', 'string', 'max:2000'],
            ],
            'vacancy' => [
                'name'    => ['required', 'string', 'max:255'],
                'phone'   => ['required', ...self::PHONE],
                'email'   => ['nullable', 'email', 'max:255'],
                'vacancy' => ['nullable', 'string', 'max:255'],
                'message' => ['nullable', 'string', 'max:3000'],
            ],
        };
    }

    /** Лист на основну пошту з «Сайт і контакти». Збій пошти не ламає відправку форми. */
    private function notify(Lead $lead): void
    {
        $to = SiteSettings::all()['emails'][0] ?? null;
        if (! $to) {
            return;
        }

        try {
            Notification::route('mail', $to)->notify(new NewLead($lead));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
