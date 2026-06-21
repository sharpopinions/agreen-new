<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('stock_status_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('currency_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('replaced_by_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();
            $table->string('sku')->unique();
            $table->decimal('price', 15, 2);
            $table->decimal('old_price', 15, 2)->nullable();
            $table->integer('preorder_days')->nullable();
            $table->string('image')->nullable();
            $table->string('og_image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
