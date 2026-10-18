<?php

namespace App\Http\Controllers;

use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

/** Особистий кабінет клієнта (Claude Design → client-cabinet.jsx). */
class AccountController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Account', [
            'seo' => Seo::make('Особистий кабінет', noindex: true),
        ]);
    }
}
