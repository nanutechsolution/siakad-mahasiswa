<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Untuk hitung status billing & lookup payment per tagihan
            $table->index(
                ['billing_id', 'status'],
                'idx_payments_billing_status'
            );

            // Untuk laporan keuangan & filter tanggal
            $table->index(
                'payment_date',
                'idx_payments_payment_date'
            );
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_billing_status');
            $table->dropIndex('idx_payments_payment_date');
        });
    }
};
