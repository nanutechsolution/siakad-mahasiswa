<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_courses', function (Blueprint $table) {
            // 1. FIX: Gunakan ULID agar konsisten dengan tabel lain
            $table->char('id', 26)->primary();

            // 2. Relasi Mahasiswa (Sudah Benar)
            $table->char('student_id', 26);
            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->cascadeOnDelete();

            // 3. FIX ERROR 3780: Ubah tipe data jadi CHAR(26)
            $table->char('curriculum_course_id', 26);
            $table->foreign('curriculum_course_id')
                ->references('id')
                ->on('curriculum_courses') // Pastikan nama tabel benar
                ->cascadeOnDelete();

            // 4. WAJIB ADA: Semester ID
            // Agar sistem tahu matkul ini diambil pada semester berapa (Ganjil 2026 / Genap 2026)
            // Asumsi Anda punya tabel 'semesters' yang juga pakai ULID
            $table->char('semester_id', 26); 
            // $table->foreign('semester_id')->references('id')->on('semesters'); // Aktifkan jika tabel semester sudah ada

            // Data Nilai
            $table->string('grade', 2)->nullable(); // A, B, C, D, E
            $table->decimal('grade_point', 3, 2)->nullable(); // 4.00, 3.00 (Untuk hitung IPK)
            $table->string('status', 20)->default('taken'); // taken | passed | failed

            $table->timestamps();

            // 5. LOGIC FIX: Unique Constraint
            // Mahasiswa boleh ambil matkul sama TAPI harus di semester berbeda (Mengulang)
            $table->unique(
                ['student_id', 'curriculum_course_id', 'semester_id'], 
                'uq_student_course_per_semester'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_courses');
    }
};