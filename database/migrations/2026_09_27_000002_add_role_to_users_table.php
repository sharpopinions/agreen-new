<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Персонал: admin | manager | content. Покупці: client | business_client | business_partner
            $table->string('role', 30)->default('client')->after('email')->index();
        });

        // Досі користувачів створювали лише для адмінки — вони адміністратори
        DB::table('users')->update(['role' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
