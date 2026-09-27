<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('number')->unique();
            // regular — звичайне замовлення; preorder — передзамовлення (без оплати й доставки, за ТЗ)
            $table->string('type', 20)->default('regular');
            $table->string('status', 30)->default('new');
            $table->string('customer_name');
            $table->string('customer_phone', 50);
            $table->string('customer_email');
            $table->string('customer_company')->nullable();
            $table->string('delivery_method', 30)->nullable();
            $table->string('delivery_city')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('payment_method', 30)->nullable();
            $table->text('comment')->nullable();
            $table->decimal('total', 15, 2)->default(0);
            $table->string('locale', 10)->nullable();
            $table->timestamps();

            $table->index(['store_id', 'status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            // Знімок товару на момент замовлення
            $table->string('sku');
            $table->string('name');
            $table->decimal('price', 15, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
