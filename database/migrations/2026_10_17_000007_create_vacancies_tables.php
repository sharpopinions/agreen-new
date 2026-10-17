<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Вакансії (Claude Design → VacanciesPage). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vacancy_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('department')->nullable();
            $table->string('city')->nullable();
            $table->string('employment')->nullable();
            $table->text('description')->nullable();
            $table->json('requirements')->nullable();
            $table->timestamps();
            $table->unique(['vacancy_id', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancy_translations');
        Schema::dropIfExists('vacancies');
    }
};
