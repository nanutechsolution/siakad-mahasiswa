<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mbkm_conversions', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            
            // Relasi ke Mahasiswa
            $table->char('student_id', 26);
            
            // Matakuliah yang "diakui" (Dikonversi)
            $table->char('curriculum_course_id', 26);
            
            // Informasi Aktivitas MBKM
            $table->string('activity_name'); 
            $table->string('partner_name')->nullable();
            
            // FIX: Gunakan char(26) agar konsisten dengan tabel academic_periods (jika nanti ada)
            $table->char('academic_period_id', 26)->nullable(); 
            
            // Hasil Konversi
            $table->integer('credit_converted'); // Berapa SKS yang diakui?
            $table->char('grade_letter', 2)->default('A'); 
            $table->decimal('grade_point', 3, 2)->default(4.00);
            
            $table->text('description')->nullable(); 
            
            $table->timestamps();

            // Aturan Unik
            $table->unique(['student_id', 'curriculum_course_id'], 'unique_mbkm_conv');

            // Foreign Keys
            $table->foreign('student_id')
                  ->references('id')
                  ->on('students')
                  ->onDelete('cascade');

            $table->foreign('curriculum_course_id')
                  ->references('id')
                  ->on('curriculum_courses')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mbkm_conversions');
    }
};