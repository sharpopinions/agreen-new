<?php

namespace App\Support;

/**
 * Поточний магазин. Зараз один (A-green, id=1, config app.store_id);
 * у SaaS-фазі тут буде визначення магазину за доменом (store_domains).
 */
class CurrentStore
{
    public static function id(): int
    {
        return (int) config('app.store_id', 1);
    }
}
