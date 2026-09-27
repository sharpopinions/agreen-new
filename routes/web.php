<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/all', [CatalogController::class, 'all'])->name('catalog.all');
Route::get('/catalog/{slug}', [CatalogController::class, 'show'])->name('catalog.category');
Route::get('/p/{slug}', [ProductController::class, 'show'])->name('product');
Route::get('/search', SearchController::class)->name('search')->middleware('throttle:60,1');
// Кошик і оформлення
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart', [CartController::class, 'store'])->name('cart.add')->middleware('throttle:120,1');
Route::patch('/cart/{product}', [CartController::class, 'update'])->whereNumber('product')->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->whereNumber('product')->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:10,1');
Route::get('/order/thanks', [CheckoutController::class, 'thanks'])->name('order.thanks');

Route::get('/about', fn() => Inertia::render('About'))->name('about');
