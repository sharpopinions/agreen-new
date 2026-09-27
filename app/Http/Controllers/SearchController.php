<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /** Швидкий пошук товарів за назвою або артикулом (для палітри Cmd+K). */
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['products' => []]);
        }

        $langId = Language::currentId();

        $products = Product::with(['translations' => fn($t) => $t->where('language_id', $langId)])
            ->where('store_id', 1)
            ->where('is_active', true)
            ->where(fn($w) => $w
                ->whereLike('sku', '%' . $q . '%')
                ->orWhereHas('translations', fn($t) => $t
                    ->where('language_id', $langId)
                    ->whereLike('name', '%' . $q . '%')))
            ->orderByDesc('reviews_count')
            ->limit(8)
            ->get()
            ->map(fn($p) => [
                'id'    => $p->id,
                'name'  => $p->translations->first()?->name ?? '',
                'slug'  => $p->translations->first()?->slug ?? '',
                'sku'   => $p->sku,
                'price' => (float) $p->price,
            ])
            ->filter(fn($p) => $p['slug'] !== '')
            ->values();

        return response()->json(['products' => $products]);
    }
}
