<?php

use App\Support\StoreDefaults;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Замовлення за планованою схемою (docs/database/future-schema.md, рішення №4):
 * статуси — таблиця order_statuses, доставка й оплата — shipping_providers /
 * payment_providers з драйверами замість кодів у коді.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_provider_translations', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
        });
        Schema::table('payment_provider_translations', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
        });

        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('key', 40);
            $table->string('color', 20)->default('#71717a');
            $table->boolean('is_final')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['store_id', 'key']);
        });

        Schema::create('order_status_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_status_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['order_status_id', 'language_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('store_id')->constrained()->nullOnDelete();
            $table->foreignId('order_status_id')->nullable()->after('type')->constrained()->nullOnDelete();
            $table->foreignId('shipping_provider_id')->nullable()->after('customer_company')->constrained()->nullOnDelete();
            $table->foreignId('payment_provider_id')->nullable()->after('delivery_address')->constrained()->nullOnDelete();
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('total');
            $table->string('payment_status', 20)->default('pending')->after('payment_provider_id');
            $table->string('tracking_number')->nullable()->after('delivery_address');
            $table->timestamp('editable_until')->nullable()->after('locale');
        });

        // Довідники для наявних магазинів + перенесення даних зі старих колонок
        foreach (DB::table('stores')->pluck('id') as $storeId) {
            StoreDefaults::install($storeId);
        }

        $statusMap = ['new' => 'pending', 'processing' => 'processing', 'shipped' => 'shipped', 'completed' => 'completed', 'cancelled' => 'cancelled'];
        $paymentMap = ['cash_on_delivery' => 'cash_on_delivery'];

        foreach (DB::table('orders')->get() as $order) {
            DB::table('orders')->where('id', $order->id)->update([
                'order_status_id' => DB::table('order_statuses')->where('store_id', $order->store_id)
                    ->where('key', $statusMap[$order->status] ?? 'pending')->value('id'),
                'shipping_provider_id' => $order->delivery_method
                    ? DB::table('shipping_providers')->where('store_id', $order->store_id)->where('driver', $order->delivery_method)->value('id')
                    : null,
                'payment_provider_id' => $order->payment_method
                    ? DB::table('payment_providers')->where('store_id', $order->store_id)->where('driver', $paymentMap[$order->payment_method] ?? $order->payment_method)->value('id')
                    : null,
            ]);
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['store_id', 'status']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['status', 'delivery_method', 'payment_method']);
            $table->index(['store_id', 'order_status_id']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['store_id', 'order_status_id']);
            $table->string('status', 30)->default('new');
            $table->string('delivery_method', 30)->nullable();
            $table->string('payment_method', 30)->nullable();
            $table->index(['store_id', 'status']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('order_status_id');
            $table->dropConstrainedForeignId('shipping_provider_id');
            $table->dropConstrainedForeignId('payment_provider_id');
            $table->dropColumn(['shipping_cost', 'payment_status', 'tracking_number', 'editable_until']);
        });
        Schema::dropIfExists('order_status_translations');
        Schema::dropIfExists('order_statuses');
        Schema::table('payment_provider_translations', fn(Blueprint $t) => $t->dropColumn('description'));
        Schema::table('shipping_provider_translations', fn(Blueprint $t) => $t->dropColumn('description'));
    }
};
