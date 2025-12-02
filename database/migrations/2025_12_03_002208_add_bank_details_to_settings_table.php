<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('campus_phone');     // Contoh: Bank BRI
            $table->string('bank_account')->nullable()->after('bank_name');     // Contoh: 1234-5678-90
            $table->string('bank_holder')->nullable()->after('bank_account');   // Contoh: Yayasan Unmaris
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account', 'bank_holder']);
        });
    }
};