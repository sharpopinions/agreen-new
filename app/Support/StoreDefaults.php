<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Типові довідники магазину: статуси замовлень, доставка, оплата (ТЗ на кошик).
 * Ідемпотентно: наявні записи (за key / driver) не змінюються — лише додаються відсутні.
 * Викликається з міграції (для наявних магазинів) і з сідера (для нових баз).
 */
class StoreDefaults
{
    public const ORDER_STATUSES = [
        // key                 color      is_final  uk / en / pl
        ['pending',          '#2563eb', false, 'Нове',            'New',              'Nowe'],
        ['confirmed',        '#0891b2', false, 'Підтверджено',    'Confirmed',        'Potwierdzone'],
        ['awaiting_payment', '#d97706', false, 'Очікує оплату',   'Awaiting payment', 'Oczekuje na płatność'],
        ['processing',       '#7c3aed', false, 'В обробці',       'Processing',       'W realizacji'],
        ['shipped',          '#0d9488', false, 'Відправлено',     'Shipped',          'Wysłane'],
        ['delivered',        '#16a34a', false, 'Доставлено',      'Delivered',        'Dostarczone'],
        ['completed',        '#15803d', true,  'Виконано',        'Completed',        'Zrealizowane'],
        ['cancelled',        '#dc2626', true,  'Скасовано',       'Cancelled',        'Anulowane'],
    ];

    public const SHIPPING = [
        // driver        requires_address  uk name / uk hint / en name / pl name
        ['nova_poshta', true,  'Нова Пошта',     '1–3 дні · за тарифами перевізника', 'Nova Poshta',    'Nova Poshta'],
        ['ukrposhta',   true,  'Укрпошта',       '3–7 днів',                          'Ukrposhta',      'Ukrposhta'],
        ['justin',      true,  'Justin',         '1–3 дні',                           'Justin',         'Justin'],
        ['meest',       true,  'Meest Express',  '1–3 дні',                           'Meest Express',  'Meest Express'],
        ['pickup',      false, 'Самовивіз',      'Київ, вул. Крайня 1',               'Pickup',         'Odbiór osobisty'],
        ['courier',     true,  "Кур'єр",         'По Києву · наступний день',         'Courier',        'Kurier'],
    ];

    public const PAYMENT = [
        // driver             roles (null = усі)                     uk / en / pl
        ['card_transfer',    null,                                  'Оплата на карту',          'Card transfer',            'Przelew na kartę'],
        ['online',           null,                                  'Онлайн оплата',            'Online payment',           'Płatność online'],
        ['invoice_vat',      null,                                  'Рахунок-фактура з ПДВ',    'Invoice with VAT',         'Faktura z VAT'],
        ['invoice_no_vat',   null,                                  'Рахунок-фактура без ПДВ',  'Invoice without VAT',      'Faktura bez VAT'],
        ['courier_cash',     null,                                  "Оплата кур'єру",           'Pay the courier',          'Płatność kurierowi'],
        ['cash_on_delivery', null,                                  'Післяплата',               'Cash on delivery',         'Za pobraniem'],
        ['deferred',         ['business_client', 'business_partner'], 'Відстрочка платежу',     'Deferred payment',         'Odroczona płatność'],
    ];

    public static function install(int $storeId): void
    {
        $langs = DB::table('languages')->where('store_id', $storeId)->pluck('id', 'code');
        if ($langs->isEmpty()) {
            return;
        }

        $now = now();

        foreach (self::ORDER_STATUSES as $i => [$key, $color, $final, $uk, $en, $pl]) {
            $id = DB::table('order_statuses')->where('store_id', $storeId)->where('key', $key)->value('id')
                ?? DB::table('order_statuses')->insertGetId([
                    'store_id' => $storeId, 'key' => $key, 'color' => $color, 'is_final' => $final,
                    'sort_order' => $i, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
                ]);
            self::names('order_status_translations', 'order_status_id', $id, $langs, compact('uk', 'en', 'pl'));
        }

        foreach (self::SHIPPING as $i => [$driver, $requiresAddress, $uk, $hint, $en, $pl]) {
            $id = DB::table('shipping_providers')->where('store_id', $storeId)->where('driver', $driver)->value('id')
                ?? DB::table('shipping_providers')->insertGetId([
                    'store_id' => $storeId, 'driver' => $driver,
                    'settings' => json_encode(['requires_address' => $requiresAddress]),
                    'sort_order' => $i, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
                ]);
            self::names('shipping_provider_translations', 'shipping_provider_id', $id, $langs, compact('uk', 'en', 'pl'), ['uk' => $hint]);
        }

        foreach (self::PAYMENT as $i => [$driver, $roles, $uk, $en, $pl]) {
            $id = DB::table('payment_providers')->where('store_id', $storeId)->where('driver', $driver)->value('id')
                ?? DB::table('payment_providers')->insertGetId([
                    'store_id' => $storeId, 'driver' => $driver,
                    'settings' => json_encode($roles ? ['roles' => $roles] : []),
                    'sort_order' => $i, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
                ]);
            self::names('payment_provider_translations', 'payment_provider_id', $id, $langs, compact('uk', 'en', 'pl'));
        }
    }

    private static function names(string $table, string $fk, int $id, $langs, array $names, array $descriptions = []): void
    {
        foreach ($names as $code => $name) {
            if (! isset($langs[$code])) {
                continue;
            }
            $exists = DB::table($table)->where($fk, $id)->where('language_id', $langs[$code])->exists();
            if ($exists) {
                continue;
            }
            $row = [$fk => $id, 'language_id' => $langs[$code], 'name' => $name, 'created_at' => now(), 'updated_at' => now()];
            if ($table !== 'order_status_translations') {
                $row['description'] = $descriptions[$code] ?? null;
            }
            DB::table($table)->insert($row);
        }
    }
}
