<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrants', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();

            // Info Pendaftaran
            $table->string('registration_no')->unique(); // PMB-2025-0001
            $table->string('period_year', 4); // 2025
            $table->string('track')->default('REGULER'); // Reguler, Prestasi

            // Pilihan Prodi
            $table->char('first_choice_id', 26);
            $table->char('second_choice_id', 26)->nullable();

            // Data Diri & Sekolah
            $table->string('nik', 20)->nullable();
            $table->string('nisn', 20)->nullable();
            $table->string('school_name')->nullable();
            $table->string('school_major')->nullable(); // IPA/IPS/SMK
            $table->float('average_grade')->default(0); // Rata-rata Rapor

            // Data Ortu
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('parent_phone')->nullable();

            // Dokumen (Simpan path dalam JSON biar fleksibel)
            // Contoh: {"ijazah": "path/...", "ktp": "path/..."}
            $table->json('documents')->nullable();

            // Status (Pakai Enum nanti di Model)
            $table->string('status')->default('DRAFT');

            $table->timestamps();

            $table->foreign('first_choice_id')
                ->references('id')
                ->on('study_programs')
                ->onDelete('restrict');

            $table->foreign('second_choice_id')
                ->references('id')
                ->on('study_programs')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrants');
    }
};
