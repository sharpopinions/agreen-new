<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ui_translation_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ui_translation_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('value')->nullable();

            $table->unique(['ui_translation_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ui_translation_values');
    }
};
