<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/all', [CatalogController::class, 'all'])->name('catalog.all');
Route::get('/catalog/{slug}', [CatalogController::class, 'show'])->name('catalog.category');
Route::get('/p/{slug}', [ProductController::class, 'show'])->name('product');
Route::get('/about', fn() => Inertia::render('About'))->name('about');
