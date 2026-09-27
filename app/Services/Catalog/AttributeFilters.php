<?php

namespace App\Services\Catalog;

use App\Models\AttributeDefinition;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Фільтри каталогу за характеристиками (EAV).
 * Набір фільтрів задає категорія (адмінка → Категорії → Фільтри); підкатегорія без власних
 * фільтрів успадковує їх від найближчої батьківської.
 *
 * URL: attr[ID][]=valueId (чекбокси, кольори, так/ні) · attr[ID][min]=1&attr[ID][max]=5 (діапазон).
 * Усередині однієї характеристики — «або», між характеристиками — «і».
 */
class AttributeFilters
{
    /** @var Collection<int, AttributeDefinition> */
    private Collection $definitions;

    /** Вибрані значення: [defId => ['values' => [ids]] | ['min' => ?float, 'max' => ?float]] */
    private array $selected = [];

    public function __construct(?Category $category, Request $request)
    {
        $this->definitions = $this->definitionsFor($category);
        $this->selected    = $this->parse((array) $request->input('attr', []));
    }

    public function selected(): array
    {
        return $this->selected;
    }

    public function isActive(): bool
    {
        return $this->selected !== [];
    }

    public function apply(Builder $query, ?int $except = null): Builder
    {
        foreach ($this->selected as $defId => $sel) {
            if ($defId === $except) {
                continue;
            }
            $ids = $this->matchingValueIds($defId, $sel);
            $query->whereHas('attributeValues', fn($q) => $q->whereIn('attribute_values.id', $ids ?: [0]));
        }

        return $query;
    }

    /**
     * Групи фільтрів для сайдбару. Кількість товарів рахується з урахуванням інших вибраних
     * характеристик (але не цієї — щоб можна було додати ще значення «або»).
     *
     * @param \Closure(): Builder $base вибірка категорії з уже застосованими звичайними фільтрами
     */
    public function facets(\Closure $base): array
    {
        return $this->definitions->map(function (AttributeDefinition $def) use ($base) {
            $scope  = $this->apply($base(), except: $def->id)->select('products.id');
            $counts = DB::table('product_attribute_values')
                ->whereIn('product_id', $scope)
                ->whereIn('attribute_value_id', $def->values->pluck('id'))
                ->groupBy('attribute_value_id')
                ->pluck(DB::raw('count(*)'), 'attribute_value_id');

            $display = $def->pivot?->display_type ?? AttributeDefinition::DEFAULT_DISPLAY[$def->type];
            $values  = $def->values
                ->map(fn(AttributeValue $v) => [
                    'id'    => $v->id,
                    'name'  => $v->name,
                    'raw'   => $v->raw,
                    'count' => (int) ($counts[$v->id] ?? 0),
                ])
                ->filter(fn($v) => $v['count'] > 0 || in_array($v['id'], $this->selected[$def->id]['values'] ?? [], true))
                ->values();

            $group = ['id' => $def->id, 'name' => $def->name, 'display' => $display];

            if ($display === 'range') {
                $numbers = $values->pluck('raw')->filter(fn($r) => is_numeric($r))->map(fn($r) => (float) $r);
                if ($numbers->isEmpty()) {
                    return null;
                }

                return $group + ['min' => $numbers->min(), 'max' => $numbers->max()];
            }

            return $values->isEmpty() ? null : $group + ['values' => $values->all()];
        })->filter()->values()->all();
    }

    private function definitionsFor(?Category $category): Collection
    {
        $langId = Language::currentId();
        $tr     = fn($q) => $q->where('language_id', $langId);

        for ($c = $category; $c; $c = $c->parent) {
            $defs = $c->attributeDefinitions()
                ->with(['translations' => $tr, 'values.translations' => $tr])
                ->where('is_active', true)
                ->where('is_filterable', true)
                ->get();
            if ($defs->isNotEmpty()) {
                return $defs;
            }
        }

        return new Collection();
    }

    private function parse(array $input): array
    {
        $selected = [];
        foreach ($input as $defId => $raw) {
            $def = $this->definitions->firstWhere('id', (int) $defId);
            if (! $def || ! is_array($raw)) {
                continue;
            }

            if (($def->pivot?->display_type) === 'range') {
                $min = isset($raw['min']) && is_numeric($raw['min']) ? (float) $raw['min'] : null;
                $max = isset($raw['max']) && is_numeric($raw['max']) ? (float) $raw['max'] : null;
                if ($min !== null || $max !== null) {
                    $selected[$def->id] = ['min' => $min, 'max' => $max];
                }
                continue;
            }

            $ids = array_values(array_intersect(
                array_map('intval', array_filter($raw, 'is_scalar')),
                $def->values->pluck('id')->all(),
            ));
            if ($ids) {
                $selected[$def->id] = ['values' => $ids];
            }
        }

        return $selected;
    }

    private function matchingValueIds(int $defId, array $sel): array
    {
        if (isset($sel['values'])) {
            return $sel['values'];
        }

        return $this->definitions->firstWhere('id', $defId)->values
            ->filter(fn(AttributeValue $v) => is_numeric($v->raw)
                && ($sel['min'] === null || (float) $v->raw >= $sel['min'])
                && ($sel['max'] === null || (float) $v->raw <= $sel['max']))
            ->pluck('id')->all();
    }
}
