<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Slug унікальний у межах мови для товарів, категорій і брендів.
 * Порожні slug ('') стають NULL — UNIQUE дозволяє кілька NULL.
 * Наявні дублікати отримують суфікс з id перекладу.
 */
return new class extends Migration
{
    private array $tables = ['product_translations', 'category_translations', 'brand_translations'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            DB::table($table)->where('slug', '')->update(['slug' => null]);

            $duplicates = DB::table($table)
                ->select('language_id', 'slug')
                ->whereNotNull('slug')
                ->groupBy('language_id', 'slug')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($duplicates as $dup) {
                DB::table($table)
                    ->where('language_id', $dup->language_id)
                    ->where('slug', $dup->slug)
                    ->orderBy('id')
                    ->skip(1)->take(PHP_INT_MAX)
                    ->get(['id', 'slug'])
                    ->each(fn($row) => DB::table($table)->where('id', $row->id)->update(['slug' => "{$row->slug}-{$row->id}"]));
            }

            Schema::table($table, function (Blueprint $t) {
                $t->unique(['language_id', 'slug']);
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropUnique(['language_id', 'slug']);
            });
        }
    }
};
