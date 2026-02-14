<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_prerequisites', function (Blueprint $table) {
            // 1. HAPUS $table->id(); 
            // Gunakan ini saja untuk Primary Key ULID:
            $table->char('id', 26)->primary();

            // 2. PERBAIKAN FOREIGN KEY
            // Jangan pakai foreignId(), pakai char(26) agar jodoh dengan induknya

            // Matakuliah Utama
            $table->char('curriculum_course_id', 26);
            $table->foreign('curriculum_course_id')
                ->references('id')
                ->on('curriculum_courses')
                ->cascadeOnDelete();

            // Matakuliah Syarat (Prasyarat)
            $table->char('prerequisite_curriculum_course_id', 26);
            $table->foreign('prerequisite_curriculum_course_id')
                ->references('id')
                ->on('curriculum_courses')
                ->cascadeOnDelete();

            $table->string('min_grade', 2)->default('D'); // Minimal nilai (misal: harus lulus C)

            $table->timestamps();

            // 3. UNIQUE CONSTRAINT
            // Mencegah input ganda: "Matkul A syaratnya Matkul B" diinput 2x
            $table->unique(
                ['curriculum_course_id', 'prerequisite_curriculum_course_id'],
                'uq_curriculum_course_prereq'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_prerequisites');
    }
};
