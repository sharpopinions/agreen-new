<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RendersProductListing;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    use RendersProductListing;

    /** Каталог: плитки категорій + усі товари з фільтрами. */
    public function index(Request $request): Response
    {
        return $this->listing($request, Language::currentId(), null, 'Catalog');
    }

    /** Усі товари з фільтрами (Figma: Catalog//All-products). */
    public function all(Request $request): Response
    {
        return $this->listing($request, Language::currentId(), null, 'CatalogCategory');
    }

    /** Категорія або підкатегорія (Figma: Catalog-category, Catalog//Subcategory). */
    public function show(Request $request, string $slug): Response
    {
        $langId = Language::currentId();

        $category = Category::with([
            'translations'          => fn($q) => $q->where('language_id', $langId),
            'parent.translations'   => fn($q) => $q->where('language_id', $langId),
            'children.translations' => fn($q) => $q->where('language_id', $langId),
        ])
            ->where('is_active', true)
            ->whereHas('translations', fn($q) => $q->where('slug', $slug)->where('language_id', $langId))
            ->firstOrFail();

        return $this->listing($request, $langId, $category, 'CatalogCategory');
    }
}
