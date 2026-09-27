<?php

namespace Database\Seeders;

use App\Models\AttributeDefinition;
use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Демо-характеристики для наявних товарів і фільтри категорій.
 * Ідемпотентний: повторний запуск нічого не дублює (пошук за українською назвою).
 */
class AttributeSeeder extends Seeder
{
    private const ATTRIBUTES = [
        // uk name, en, pl, type, values: [uk, en, pl, raw]
        'grit' => ['Зернистість', 'Grit', 'Gradacja', 'select', [
            ['P80', 'P80', 'P80', null], ['P120', 'P120', 'P120', null], ['P240', 'P240', 'P240', null], ['P400', 'P400', 'P400', null],
        ]],
        'system' => ['Тип системи', 'System type', 'Typ systemu', 'select', [
            ['1K', '1K', '1K', null], ['2K', '2K', '2K', null],
        ]],
        'color' => ['Колір', 'Color', 'Kolor', 'color', [
            ['Білий', 'White', 'Biały', '#ffffff'], ['Сірий', 'Grey', 'Szary', '#9ca3af'], ['Чорний', 'Black', 'Czarny', '#18181b'],
        ]],
        'volume' => ['Обʼєм, л', 'Volume, l', 'Objętość, l', 'number', [
            ['0,5 л', '0.5 l', '0,5 l', '0.5'], ['1 л', '1 l', '1 l', '1'], ['5 л', '5 l', '5 l', '5'],
        ]],
    ];

    /** SKU => [attribute key => [uk value…]] */
    private const PRODUCTS = [
        'MA-120'       => ['grit' => ['P120']],
        'MIR-SAND-080' => ['grit' => ['P80']],
        'NOV-GRUN-001' => ['system' => ['1K'], 'color' => ['Сірий'], 'volume' => ['1 л']],
        'NOV-LAK-001'  => ['system' => ['2K'], 'volume' => ['1 л']],
        'VP-08'        => ['system' => ['2K'], 'color' => ['Білий']],
        '3M-P3'        => ['volume' => ['1 л']],
        'DT-5L'        => ['volume' => ['5 л']],
    ];

    /** slug категорії (uk) => [attribute key => вигляд фільтра] */
    private const FILTERS = [
        'abrazyvni-materialy'   => ['grit' => 'checkbox'],
        'lakofarbovi-materialy' => ['system' => 'checkbox', 'color' => 'color_swatch', 'volume' => 'range'],
    ];

    public function run(): void
    {
        \Illuminate\Support\Facades\DB::transaction(fn() => $this->seed());
    }

    private function seed(): void
    {
        $langs = Language::query()->pluck('id', 'code');
        $defs  = [];
        $values = [];

        foreach (self::ATTRIBUTES as $key => [$uk, $en, $pl, $type, $items]) {
            $def = AttributeDefinition::whereHas('translations', fn($q) => $q->where('name', $uk))->first()
                ?? AttributeDefinition::create(['type' => $type, 'is_filterable' => true, 'sort_order' => count($defs), 'is_active' => true]);
            $this->names($def->translations(), $langs, compact('uk', 'en', 'pl'));
            $defs[$key] = $def;

            foreach ($items as $i => [$vUk, $vEn, $vPl, $raw]) {
                $value = $def->values()->whereHas('translations', fn($q) => $q->where('name', $vUk))->first()
                    ?? $def->values()->create(['sort_order' => $i]);
                $this->names($value->translations(), $langs, ['uk' => $vUk, 'en' => $vEn, 'pl' => $vPl], withValue: true, raw: $raw);
                $values[$key][$vUk] = $value->id;
            }
        }

        foreach (self::PRODUCTS as $sku => $attrs) {
            $product = Product::where('sku', $sku)->first();
            if (! $product) {
                continue;
            }
            $ids = collect($attrs)->flatMap(fn($names, $key) => collect($names)->map(fn($n) => $values[$key][$n]));
            $product->attributeValues()->syncWithoutDetaching($ids);
        }

        foreach (self::FILTERS as $slug => $filters) {
            $category = Category::whereHas('translations', fn($q) => $q->where('slug', $slug))->first();
            if (! $category) {
                continue;
            }
            $sync = collect(array_keys($filters))->values()->mapWithKeys(fn($key, $i) => [
                $defs[$key]->id => ['display_type' => $filters[$key], 'sort_order' => $i],
            ]);
            $category->attributeDefinitions()->syncWithoutDetaching($sync);
        }
    }

    private function names($relation, $langs, array $names, bool $withValue = false, ?string $raw = null): void
    {
        foreach ($names as $code => $name) {
            if (! isset($langs[$code])) {
                continue;
            }
            $attrs = ['name' => $name];
            if ($withValue) {
                $attrs['value'] = $raw;
            }
            $relation->updateOrCreate(['language_id' => $langs[$code]], $attrs);
        }
    }
}
