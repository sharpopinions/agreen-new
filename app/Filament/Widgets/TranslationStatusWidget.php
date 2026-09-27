<?php

namespace App\Filament\Widgets;

use App\Models\Badge;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use App\Models\StockStatus;
use Filament\Widgets\Widget;

class TranslationStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.translation-status';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 10;

    public function getViewData(): array
    {
        $languages = Language::query()->where('is_active', true)->orderBy('id')->get();

        $entities = [
            'Категорії'        => Category::class,
            'Бренди'           => Brand::class,
            'Бейджі'           => Badge::class,
            'Товари'           => Product::class,
            'Статуси наявності' => StockStatus::class,
        ];

        $rows = [];

        foreach ($entities as $label => $modelClass) {
            $total = $modelClass::query()->count();
            $row = ['label' => $label, 'total' => $total, 'languages' => []];

            foreach ($languages as $lang) {
                $filled = $modelClass::query()
                    ->whereHas('translations', fn($q) => $q
                        ->where('language_id', $lang->id)
                        ->where('name', '!=', '')
                        ->whereNotNull('name')
                    )
                    ->count();

                $row['languages'][] = [
                    'code'   => $lang->code,
                    'filled' => $filled,
                    'total'  => $total,
                    'done'   => $total > 0 && $filled === $total,
                ];
            }

            $rows[] = $row;
        }

        return ['languages' => $languages, 'rows' => $rows];
    }
}
