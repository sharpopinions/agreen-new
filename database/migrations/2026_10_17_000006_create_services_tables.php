<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Послуги (Claude Design → ServicesPage, StudyCenterPage) і відгуки клієнтів. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('icon', 16)->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('service_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('summary')->nullable();
            $table->json('details')->nullable();   // ["Теоретичні та практичні заняття", …]
            $table->longText('body')->nullable();
            $table->json('stats')->nullable();     // [{value, label}]
            $table->json('benefits')->nullable();  // [{icon, title, text}]
            $table->json('courses')->nullable();   // [{title, duration, level, price}]
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->unique(['service_id', 'language_id']);
            $table->unique(['language_id', 'slug']);
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('author');
            $table->text('text');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('service_translations');
        Schema::dropIfExists('services');
    }
};
