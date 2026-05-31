<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home', [
        'title' => 'A-green',
    ]);
});

Route::get('/catalog', function(){
    return Inertia::render('Catalog');
})->name('catalog');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');
