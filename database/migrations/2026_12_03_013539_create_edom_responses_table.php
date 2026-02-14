<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Hapus tabel lama jika ada agar bersih
        Schema::dropIfExists('edom_responses');

        Schema::create('edom_responses', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID

            // --- FIX FOREIGN KEYS (Semua harus CHAR 26) ---
            $table->char('academic_period_id', 26);
            $table->char('student_id', 26);
            $table->char('course_class_id', 26); // Pengganti classroom_id
            $table->char('lecturer_id', 26)->nullable(); // Penting: Untuk Team Teaching

            // Data Penilaian (Scalable Approach)
            // Format JSON: {"q1": 5, "q2": 4, "q3": 5}
            // Hemat baris database signifikan (1 baris vs 20 baris per mahasiswa)
            $table->json('answers'); 
            
            $table->text('comments')->nullable(); // Kritik & Saran
            
            $table->timestamps();

            // Aturan: Satu mahasiswa hanya boleh menilai satu dosen di satu kelas sekali saja
            $table->unique(['student_id', 'course_class_id', 'lecturer_id'], 'unique_edom_entry');

            // --- DEFINISI FOREIGN KEYS ---
            $table->foreign('academic_period_id')
                  ->references('id')
                  ->on('academic_periods')
                  ->onDelete('cascade');

            $table->foreign('student_id')
                  ->references('id')
                  ->on('students')
                  ->onDelete('cascade');

            $table->foreign('course_class_id')
                  ->references('id')
                  ->on('course_classes') // Pastikan tabel ini sudah dibuat (migrasi schedule)
                  ->onDelete('cascade');

            $table->foreign('lecturer_id')
                  ->references('id')
                  ->on('lecturers')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edom_responses');
    }
};