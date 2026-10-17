<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Сторінка бренду (Claude Design → BrandDetailPage): короткий опис у шапці й цифри. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brand_translations', function (Blueprint $table) {
            $table->text('short_description')->nullable()->after('slug');
            $table->json('stats')->nullable()->after('description');
        });
        Schema::table('brand_media', function (Blueprint $table) {
            $table->index(['brand_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('brand_media', fn(Blueprint $t) => $t->dropIndex(['brand_id', 'sort_order']));
        Schema::table('brand_translations', fn(Blueprint $t) => $t->dropColumn(['short_description', 'stats']));
    }
};
