<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Pastikan tabel bersih dulu
        Schema::dropIfExists('billings');

        Schema::create('billings', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            
            // Relasi ke Mahasiswa atau Pendaftar
            $table->char('student_id', 26)->nullable();
            $table->char('registrant_id', 26)->nullable(); 
            
            // Relasi ke Tarif
            $table->char('tuition_rate_id', 26)->nullable();
            
            // Periode Akademik
            $table->char('academic_period_id', 26)->nullable(); 
            
            $table->string('billing_code', 20)->unique(); // Kode VA
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable(); 
            
            $table->enum('status', ['unpaid', 'paid', 'cancelled', 'expired'])->default('unpaid');
            $table->dateTime('paid_at')->nullable();
            
            $table->timestamps();

            // Indexing
            $table->index(['student_id', 'academic_period_id']);
            $table->index('billing_code');

            // Foreign Keys
            $table->foreign('student_id')
                  ->references('id')
                  ->on('students')
                  ->onDelete('cascade');

            $table->foreign('registrant_id')
                  ->references('id')
                  ->on('registrants')
                  ->onDelete('cascade');

            $table->foreign('tuition_rate_id')
                  ->references('id')
                  ->on('tuition_rates')
                  ->onDelete('set null');
                  
             // Uncomment jika tabel academic_periods sudah ada
             // $table->foreign('academic_period_id')->references('id')->on('academic_periods');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};