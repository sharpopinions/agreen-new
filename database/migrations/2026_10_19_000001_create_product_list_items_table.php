<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Вішліст і порівняння зареєстрованих покупців (гості — у сесії)
        Schema::create('product_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('list', 20);
            $table->timestamps();
            $table->unique(['user_id', 'list', 'product_id']);
            $table->index(['product_id', 'list']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_list_items');
    }
};
