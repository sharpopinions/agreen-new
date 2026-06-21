<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Slug — опціональне поле для не-дефолтної мови.
        // NULL дозволяє UNIQUE індексу мати кілька порожніх значень (на відміну від '').
        Schema::table('category_translations', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
        });

        Schema::table('brand_translations', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
        });

        Schema::table('product_translations', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
            // Прибираємо UNIQUE(language_id, slug) — пусті slug конфліктують.
            // Унікальність гарантується через UNIQUE(product_id, language_id).
            $table->dropUnique(['language_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });

        Schema::table('brand_translations', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });

        Schema::table('product_translations', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
            $table->unique(['language_id', 'slug']);
        });
    }
};
