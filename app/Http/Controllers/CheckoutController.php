<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PaymentProvider;
use App\Models\ShippingProvider;
use App\Services\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function show(): Response|RedirectResponse
    {
        if ($this->cart->count() === 0) {
            return redirect()->route('cart');
        }

        // Способи доставки й оплати керуються з адмінки (Налаштування → Доставка / Оплата)
        return Inertia::render('Checkout', [
            'deliveryMethods' => ShippingProvider::available()->map(fn(ShippingProvider $p) => [
                'id'              => $p->id,
                'name'            => $p->name,
                'hint'            => $p->description,
                'requiresAddress' => $p->requiresAddress(),
            ]),
            'paymentMethods' => PaymentProvider::availableFor(auth()->user())->map(fn(PaymentProvider $p) => [
                'id'   => $p->id,
                'name' => $p->name,
                'hint' => $p->description,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $items = $this->cart->items();
        if ($items->isEmpty()) {
            return redirect()->route('cart');
        }

        $regular  = $items->where('preorder', false)->values();
        $preorder = $items->where('preorder', true)->values();

        // Доставка й оплата потрібні лише для звичайного замовлення (передзамовлення — без них, ТЗ)
        $needsShipping = $regular->isNotEmpty();

        $shipping = ShippingProvider::available();
        $payment  = PaymentProvider::availableFor($request->user());
        $selectedShipping = $shipping->firstWhere('id', (int) $request->input('delivery'));

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'phone'    => ['required', 'string', 'max:50', 'regex:/^[0-9+()\-\s]{9,20}$/'],
            'email'    => ['required', 'email', 'max:255'],
            'company'  => ['nullable', 'string', 'max:255'],
            'delivery' => ['bail', Rule::requiredIf($needsShipping), 'nullable', 'integer', Rule::in($shipping->pluck('id')->all())],
            'city'     => [Rule::requiredIf($needsShipping && ($selectedShipping?->requiresAddress() ?? true)), 'nullable', 'string', 'max:255'],
            'address'  => ['nullable', 'string', 'max:255'],
            'payment'  => ['bail', Rule::requiredIf($needsShipping), 'nullable', 'integer', Rule::in($payment->pluck('id')->all())],
            'comment'  => ['nullable', 'string', 'max:2000'],
        ], [
            'phone.regex' => 'Вкажіть телефон у форматі +380 XX XXX XX XX.',
        ]);

        $numbers = DB::transaction(function () use ($data, $regular, $preorder) {
            $numbers = [];

            if ($regular->isNotEmpty()) {
                $numbers[] = $this->createOrder('regular', $regular, $data);
            }
            if ($preorder->isNotEmpty()) {
                $numbers[] = $this->createOrder('preorder', $preorder, $data);
            }

            return $numbers;
        });

        $this->cart->clear();
        session(['last_orders' => $numbers]);

        return redirect()->route('order.thanks');
    }

    public function thanks(): Response|RedirectResponse
    {
        $numbers = session('last_orders', []);
        if (! $numbers) {
            return redirect()->route('home');
        }

        $orders = Order::with(['items', 'shippingProvider.translations', 'paymentProvider.translations'])
            ->whereIn('number', $numbers)->get()->map(fn(Order $o) => [
            'number'   => $o->number,
            'type'     => $o->type,
            'delivery' => $o->shippingProvider?->name,
            'payment'  => $o->paymentProvider?->name,
            'total'    => (float) $o->total,
            'items'    => $o->items->map(fn($i) => [
                'name'     => $i->name,
                'sku'      => $i->sku,
                'quantity' => $i->quantity,
                'total'    => (float) $i->total,
            ]),
        ]);

        return Inertia::render('OrderThanks', ['orders' => $orders]);
    }

    private function createOrder(string $type, $items, array $data): string
    {
        $isPreorder = $type === 'preorder';

        $order = Order::create([
            'user_id'          => auth()->id(),
            'number'           => 'tmp-' . uniqid('', true),
            'type'             => $type,
            'order_status_id'  => OrderStatus::initialId(),
            'customer_name'    => $data['name'],
            'customer_phone'   => $data['phone'],
            'customer_email'   => $data['email'],
            'customer_company' => $data['company'] ?? null,
            'shipping_provider_id' => $isPreorder ? null : $data['delivery'],
            'delivery_city'    => $isPreorder ? null : ($data['city'] ?? null),
            'delivery_address' => $isPreorder ? null : ($data['address'] ?? null),
            'payment_provider_id'  => $isPreorder ? null : $data['payment'],
            'comment'          => $data['comment'] ?? null,
            'total'            => round($items->sum('total'), 2),
            'locale'           => app()->getLocale(),
        ]);

        $order->update(['number' => Order::makeNumber($order->id)]);

        $order->items()->createMany($items->map(fn($i) => [
            'product_id' => $i['id'],
            'sku'        => $i['sku'],
            'name'       => $i['name'],
            'price'      => $i['price'],
            'quantity'   => $i['quantity'],
            'total'      => $i['total'],
        ])->all());

        return $order->number;
    }
}
