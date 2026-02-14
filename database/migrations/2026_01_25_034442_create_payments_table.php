<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID

            // Relasi ke Tagihan
            $table->char('billing_id', 26);
            
            $table->decimal('amount_paid', 12, 0); // Jumlah Bayar
            $table->string('payment_method')->default('TRANSFER'); // TRANSFER, CASH, MIDTRANS
            $table->string('proof_path')->nullable(); // Foto Bukti
            $table->date('payment_date');

            // Status Verifikasi
            $table->enum('status', ['PENDING', 'VERIFIED', 'REJECTED'])->default('PENDING');
            $table->string('rejection_note')->nullable();

            // Relasi ke User Admin (Verifikator)
            // Pastikan tabel 'users' Anda juga sudah pakai ULID. 
            // Jika users masih BigInt, ganti ini jadi unsignedBigInteger('verified_by')
            $table->char('verified_by', 26)->nullable(); 
            
            $table->timestamps();

            // Foreign Keys
            $table->foreign('billing_id')
                  ->references('id')
                  ->on('billings')
                  ->onDelete('cascade');

            // $table->foreign('verified_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};