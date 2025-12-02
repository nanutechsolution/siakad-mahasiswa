<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_prerequisites', function (Blueprint $table) {
            // Matkul Utama (Contoh: Web 2)
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            // Matkul Syaratnya (Contoh: Web 1)
            $table->foreignId('prerequisite_course_id')->constrained('courses')->cascadeOnDelete();

            // Syarat Nilai Minimal (Opsional, misal harus minimal C)
            $table->char('min_grade', 2)->default('D');

            $table->primary(['course_id', 'prerequisite_course_id']); // Gabungan PK
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_prerequisites');
    }
};
