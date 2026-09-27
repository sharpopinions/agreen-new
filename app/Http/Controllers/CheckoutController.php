<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

        return Inertia::render('Checkout', [
            'deliveryMethods' => Order::DELIVERY_METHODS,
            'paymentMethods'  => Order::PAYMENT_METHODS,
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

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'phone'    => ['required', 'string', 'max:50', 'regex:/^[0-9+()\-\s]{9,20}$/'],
            'email'    => ['required', 'email', 'max:255'],
            'company'  => ['nullable', 'string', 'max:255'],
            'delivery' => [Rule::requiredIf($needsShipping), 'nullable', Rule::in(array_keys(Order::DELIVERY_METHODS))],
            'city'     => [Rule::requiredIf($needsShipping && $request->input('delivery') !== 'pickup'), 'nullable', 'string', 'max:255'],
            'address'  => ['nullable', 'string', 'max:255'],
            'payment'  => [Rule::requiredIf($needsShipping), 'nullable', Rule::in(array_keys(Order::PAYMENT_METHODS))],
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

        $orders = Order::with('items')->whereIn('number', $numbers)->get()->map(fn(Order $o) => [
            'number'   => $o->number,
            'type'     => $o->type,
            'delivery' => Order::DELIVERY_METHODS[$o->delivery_method] ?? null,
            'payment'  => Order::PAYMENT_METHODS[$o->payment_method] ?? null,
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
            'store_id'         => 1,
            'number'           => 'tmp-' . uniqid('', true),
            'type'             => $type,
            'status'           => 'new',
            'customer_name'    => $data['name'],
            'customer_phone'   => $data['phone'],
            'customer_email'   => $data['email'],
            'customer_company' => $data['company'] ?? null,
            'delivery_method'  => $isPreorder ? null : $data['delivery'],
            'delivery_city'    => $isPreorder ? null : ($data['city'] ?? null),
            'delivery_address' => $isPreorder ? null : ($data['address'] ?? null),
            'payment_method'   => $isPreorder ? null : $data['payment'],
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
