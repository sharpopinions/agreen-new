<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Покупці (Claude Design → AuthModal, ClientCabinet): профіль, підтвердження email кодом, заявка на бізнес-роль. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->nullable()->after('name');
            $table->string('phone', 30)->nullable()->unique()->after('email');
            $table->string('company')->nullable()->after('phone');
            $table->string('company_code', 20)->nullable()->after('company'); // ЄДРПОУ / ІПН
            // Заявка на бізнес-роль: business_client | business_partner — чекає підтвердження менеджера
            $table->string('requested_role', 30)->nullable()->after('role');
            $table->timestamp('role_requested_at')->nullable()->after('requested_role');
            // Типові доставка й оплата для оформлення замовлення
            $table->foreignId('default_shipping_provider_id')->nullable()->constrained('shipping_providers')->nullOnDelete();
            $table->foreignId('default_payment_provider_id')->nullable()->constrained('payment_providers')->nullOnDelete();
            $table->string('default_city')->nullable();
            $table->string('default_address')->nullable();
            // Сповіщення: {"email_orders":true,"sms_orders":true,"newsletter":false}
            $table->json('notifications')->nullable();
            // Код підтвердження email (зберігається хешем)
            $table->string('email_code')->nullable();
            $table->timestamp('email_code_sent_at')->nullable();
            $table->unsignedTinyInteger('email_code_attempts')->default(0);
            $table->timestamp('last_login_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_shipping_provider_id');
            $table->dropConstrainedForeignId('default_payment_provider_id');
            $table->dropUnique(['phone']);
            $table->dropColumn([
                'last_name', 'phone', 'company', 'company_code', 'requested_role', 'role_requested_at',
                'default_city', 'default_address', 'notifications',
                'email_code', 'email_code_sent_at', 'email_code_attempts', 'last_login_at',
            ]);
        });
    }
};
