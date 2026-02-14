<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. HEADER KRS (Lembar Kartu Rencana Studi)
        // Menyimpan status pengajuan KRS per semester
        Schema::create('study_plans', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            
            $table->char('student_id', 26);
            $table->char('academic_period_id', 26); // Tahun Ajaran (Misal: 20241)
            
            // Status Flow KRS:
            // draft     = Mahasiswa isi KRS
            // submitted = Ajukan ke Dosen Wali
            // approved  = Disetujui (Resmi masuk kelas)
            // rejected  = Ditolak (Revisi)
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            
            $table->text('notes')->nullable(); // Catatan revisi dari Dosen Wali
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            
            // Aturan: Satu mahasiswa hanya boleh punya 1 lembar KRS per semester
            $table->unique(['student_id', 'academic_period_id']);
            
            // Relasi
            $table->foreign('student_id')
                  ->references('id')
                  ->on('students')
                  ->onDelete('cascade');
            
            // Uncomment jika tabel academic_periods sudah siap
            // $table->foreign('academic_period_id')->references('id')->on('academic_periods');
        });

        // 2. DETAIL KRS & NILAI (Item Matakuliah yang diambil)
        // Menggabungkan pengambilan kelas dan nilai akhirnya
        Schema::create('study_plan_details', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            
            $table->char('study_plan_id', 26); // Link ke Header KRS
            $table->char('course_class_id', 26); // Link ke Kelas Jadwal (Course Class)
            
            // --- DATA NILAI (Disatukan disini agar aman) ---
            $table->decimal('score_number', 5, 2)->nullable(); // Nilai Angka (0-100)
            $table->char('grade_letter', 2)->nullable(); // A, B+, C, D, E, TL
            $table->decimal('grade_point', 3, 2)->nullable(); // 4.00, 3.50
            
            $table->boolean('is_published')->default(false); // Nilai sudah dirilis ke mahasiswa?
            
            $table->timestamps();

            // Aturan: Dalam satu KRS, tidak boleh ambil kelas yang sama 2x
            $table->unique(['study_plan_id', 'course_class_id'], 'unique_krs_item');
            
            // Relasi
            $table->foreign('study_plan_id')
                  ->references('id')
                  ->on('study_plans')
                  ->onDelete('cascade');

            $table->foreign('course_class_id')
                  ->references('id')
                  ->on('course_classes')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_plan_details');
        Schema::dropIfExists('study_plans');
    }
};