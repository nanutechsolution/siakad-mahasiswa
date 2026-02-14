<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. KELAS PERKULIAHAN (Jadwal Fisik)
        // Contoh: Algoritma (IF101) - Kelas A
        Schema::create('course_classes', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            
            $table->char('academic_period_id', 26); // Tahun Ajaran (20241)
            $table->char('course_id', 26); // Link ke Master Matkul (courses)
            
            $table->string('name', 50); // Nama Kelas (A, B, Pagi, Karyawan)
            $table->integer('quota')->default(40);
            
            // Cache jumlah mahasiswa (biar gak lemot count terus)
            $table->integer('enrolled_count')->default(0); 
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();

            // Aturan Unik:
            // Di semester 20241, Matkul Algoritma, tidak boleh ada 2 kelas bernama "A"
            $table->unique(['academic_period_id', 'course_id', 'name'], 'unique_class_instance');
            
            // Foreign Keys
            $table->foreign('course_id')
                  ->references('id')
                  ->on('courses')
                  ->onDelete('cascade');
                  
            // Uncomment jika tabel academic_periods sudah siap & pakai ULID
            // $table->foreign('academic_period_id')->references('id')->on('academic_periods');
        });

        // 2. DOSEN PENGAMPU (Team Teaching)
        // Satu kelas bisa diajar banyak dosen
        Schema::create('class_lecturers', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            
            $table->char('course_class_id', 26);
            $table->char('lecturer_id', 26);
            
            $table->boolean('is_primary')->default(false); // Dosen Koordinator?
            $table->boolean('can_input_grade')->default(true); // Hak akses input nilai
            
            $table->timestamps();

            $table->foreign('course_class_id')
                  ->references('id')
                  ->on('course_classes')
                  ->onDelete('cascade');
                  
            $table->foreign('lecturer_id')
                  ->references('id')
                  ->on('lecturers')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_lecturers');
        Schema::dropIfExists('course_classes');
    }
};