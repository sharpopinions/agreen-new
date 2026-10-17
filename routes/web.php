<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\VacancyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/all', [CatalogController::class, 'all'])->name('catalog.all');
Route::get('/catalog/{slug}', [CatalogController::class, 'show'])->name('catalog.category');
Route::get('/p/{slug}', [ProductController::class, 'show'])->name('product');
// Бренди
Route::get('/brands', [BrandController::class, 'index'])->name('brands');
Route::get('/brands/{slug}', [BrandController::class, 'show'])->name('brand');
Route::get('/brands/{slug}/about', [BrandController::class, 'about'])->name('brand.about');
Route::get('/brands/{slug}/media', [BrandController::class, 'media'])->name('brand.media');
// Блог
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.post');
// Послуги
Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('service');
// Акції
Route::get('/sale', [PromotionController::class, 'index'])->name('promotions');
Route::get('/sale/{slug}', [PromotionController::class, 'show'])->name('promotion');
Route::get('/search', SearchController::class)->name('search')->middleware('throttle:60,1');
// Кошик і оформлення
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart', [CartController::class, 'store'])->name('cart.add')->middleware('throttle:120,1');
Route::patch('/cart/{product}', [CartController::class, 'update'])->whereNumber('product')->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->whereNumber('product')->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:10,1');
Route::get('/vacancies', [VacancyController::class, 'index'])->name('vacancies');
Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');
// Заявки з форм сайту (контакти, швидке замовлення, партнерство…)
Route::post('/forms/{type}', [LeadController::class, 'store'])->name('forms.store')->middleware('throttle:5,1');
Route::get('/order/thanks', [CheckoutController::class, 'thanks'])->name('order.thanks');

// Інформаційні сторінки (адмінка → Контент → Сторінки). Останнім — щоб не перехоплювати інші адреси.
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->name('page');

// Невідомі адреси з кількох сегментів — 404 через web-middleware (сесія, кошик, меню доступні)
Route::fallback(fn() => abort(404));
