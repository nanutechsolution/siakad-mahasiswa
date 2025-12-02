<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edom_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_period_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('student_id')->constrained()->cascadeOnDelete();
            
            // Kunci Utama: Mahasiswa menilai Kelas spesifik (Dosennya)
            $table->foreignUlid('classroom_id')->constrained()->cascadeOnDelete();
            
            $table->foreignId('edom_question_id')->constrained()->cascadeOnDelete();
            
            $table->tinyInteger('score'); // Skala 1-5 (Sangat Buruk - Sangat Baik)
            
            $table->timestamps();

            // Mencegah duplikasi: 1 Mhs, 1 Kelas, 1 Pertanyaan = 1 Jawaban
            $table->unique(['student_id', 'classroom_id', 'edom_question_id'], 'edom_unique_answer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edom_responses');
    }
};