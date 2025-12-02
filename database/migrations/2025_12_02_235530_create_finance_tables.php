<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Tagihan (Billings)
        Schema::create('billings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_period_id')->nullable()->constrained(); // Opsional, misal uang gedung tidak terikat semester

            $table->string('title'); // Contoh: "SPP Semester Ganjil 2024"
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 0); // Nominal (Rp)
            $table->date('due_date')->nullable(); // Jatuh Tempo

            // Status Tagihan: UNPAID (Belum), PARTIAL (Cicil), PAID (Lunas)
            $table->enum('status', ['UNPAID', 'PARTIAL', 'PAID'])->default('UNPAID');

            $table->timestamps();
        });

        // 2. Tabel Pembayaran (Payments)
        Schema::create('payments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('billing_id')->constrained()->cascadeOnDelete();

            $table->decimal('amount_paid', 12, 0); // Jumlah yang dibayar
            $table->string('payment_method')->default('TRANSFER'); // TRANSFER, CASH
            $table->string('proof_path')->nullable(); // Foto Bukti Transfer
            $table->date('payment_date');

            // Status Verifikasi: PENDING (Menunggu Admin), VERIFIED (Sah), REJECTED (Ditolak)
            $table->enum('status', ['PENDING', 'VERIFIED', 'REJECTED'])->default('PENDING');
            $table->string('rejection_note')->nullable(); // Alasan jika ditolak

            $table->foreignUlid('verified_by')->nullable()->constrained('users'); // Siapa admin yang memverifikasi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('billings');
    }
};
