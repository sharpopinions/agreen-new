<?php

namespace App\Services;

use App\Models\CashbackTransaction;
use App\Models\Order;
use App\Models\User;
use App\Support\SiteSettings;

/**
 * Кешбек клієнта: рівень за оборотом (сума виконаних замовлень), баланс,
 * автоматичне нарахування при переході замовлення у статус «Виконано».
 */
class Cashback
{
    public const COMPLETED_STATUS = 'completed';

    /** Рівні поточною мовою, від меншого обороту до більшого. */
    public function levels(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        return collect(SiteSettings::all()['cashback_levels'] ?? [])
            ->map(fn($l) => [
                'name'    => is_array($l['name'] ?? null) ? ($l['name'][$locale] ?? $l['name']['uk'] ?? '') : (string) ($l['name'] ?? ''),
                'from'    => (float) ($l['from'] ?? 0),
                'percent' => (float) ($l['percent'] ?? 0),
            ])
            ->sortBy('from')->values()->all();
    }

    public function turnover(User $user): float
    {
        return (float) $user->orders()
            ->whereHas('status', fn($q) => $q->where('key', self::COMPLETED_STATUS))
            ->sum('total');
    }

    public function balance(User $user): float
    {
        return (float) $user->cashbackTransactions()->sum('amount');
    }

    /** Поточний і наступний рівень + прогрес. */
    public function summary(User $user): array
    {
        $levels   = $this->levels();
        $turnover = $this->turnover($user);
        $current  = collect($levels)->last(fn($l) => $turnover >= $l['from']);
        $next     = collect($levels)->first(fn($l) => $l['from'] > $turnover);

        return [
            'balance'  => $this->balance($user),
            'turnover' => $turnover,
            'level'    => $current,
            'next'     => $next,
            'progress' => $next ? min(100, round($turnover / max($next['from'], 1) * 100)) : 100,
        ];
    }

    /** Нараховує кешбек за виконане замовлення (один раз на замовлення). */
    public function accrue(Order $order): ?CashbackTransaction
    {
        $user = $order->user;
        if (! $user || $user->isStaff() || CashbackTransaction::where('order_id', $order->id)->where('amount', '>', 0)->exists()) {
            return null;
        }

        // Рівень рахуємо за оборотом до цього замовлення
        $before  = $this->turnover($user) - (float) $order->total;
        $percent = collect($this->levels('uk'))->last(fn($l) => $before >= $l['from'])['percent'] ?? 0;
        $amount  = round((float) $order->total * $percent / 100, 2);

        if ($amount <= 0) {
            return null;
        }

        return CashbackTransaction::create([
            'store_id'    => $order->store_id,
            'user_id'     => $user->id,
            'order_id'    => $order->id,
            'amount'      => $amount,
            'description' => 'Кешбек за #' . $order->number,
        ]);
    }
}
