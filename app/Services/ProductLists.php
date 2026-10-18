<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Вішліст (wishlist) і порівняння (compare).
 * Гість — список id у сесії; покупець — таблиця product_list_items.
 * Після входу сесійні списки переносяться в профіль (mergeSessionInto).
 */
class ProductLists
{
    public const LISTS = ['wishlist', 'compare'];

    /** Скільки товарів однієї категорії можна порівнювати одночасно (Claude Design → ComparePage). */
    public const COMPARE_PER_CATEGORY = 4;

    public const MAX_ITEMS = 100;

    private const TABLE = 'product_list_items';

    /** @return int[] id товарів, нові — першими */
    public function ids(string $list): array
    {
        if ($user = $this->user()) {
            return DB::table(self::TABLE)->where('user_id', $user->id)->where('list', $list)
                ->orderByDesc('id')->pluck('product_id')->map(fn($id) => (int) $id)->all();
        }

        return array_values(session($this->key($list), []));
    }

    public function has(string $list, int $productId): bool
    {
        return in_array($productId, $this->ids($list), true);
    }

    public function add(string $list, int $productId): void
    {
        if ($this->has($list, $productId) || count($this->ids($list)) >= self::MAX_ITEMS) {
            return;
        }

        if ($user = $this->user()) {
            DB::table(self::TABLE)->insertOrIgnore([
                'user_id' => $user->id, 'product_id' => $productId, 'list' => $list,
                'created_at' => now(), 'updated_at' => now(),
            ]);

            return;
        }

        session([$this->key($list) => array_merge([$productId], $this->ids($list))]);
    }

    public function remove(string $list, int|array $productIds): void
    {
        $productIds = (array) $productIds;

        if ($user = $this->user()) {
            DB::table(self::TABLE)->where('user_id', $user->id)->where('list', $list)->whereIn('product_id', $productIds)->delete();

            return;
        }

        session([$this->key($list) => array_values(array_diff($this->ids($list), $productIds))]);
    }

    public function clear(string $list): void
    {
        $this->remove($list, $this->ids($list));
    }

    /** Переносить сесійні списки гостя в профіль після входу / реєстрації. */
    public function mergeSessionInto(User $user): void
    {
        foreach (self::LISTS as $list) {
            $ids = session()->pull($this->key($list), []);
            if (! $ids) {
                continue;
            }
            DB::table(self::TABLE)->insertOrIgnore(collect($ids)->map(fn($id) => [
                'user_id' => $user->id, 'product_id' => $id, 'list' => $list,
                'created_at' => now(), 'updated_at' => now(),
            ])->all());
        }
    }

    public function summary(): array
    {
        return [
            'wishlist' => $this->ids('wishlist'),
            'compare'  => $this->ids('compare'),
        ];
    }

    private function user(): ?User
    {
        return auth()->user();
    }

    private function key(string $list): string
    {
        return 'lists.' . $list;
    }
}
