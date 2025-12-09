<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bank Soal
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question_text');
            // Pilihan Jawaban (A, B, C, D)
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->char('correct_answer', 1); // A, B, C, D
            $table->integer('points')->default(5); // Bobot nilai
            $table->timestamps();
        });

        // 2. Jadwal Ujian
        // Tidak perlu tabel jadwal rumit, kita pakai setting global PMB saja atau relasi ke Gelombang

        // 3. Hasil Ujian (Jawaban Peserta)
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('registrant_id')->constrained()->cascadeOnDelete();
            
            $table->dateTime('started_at');
            $table->dateTime('finished_at')->nullable();
            $table->integer('total_score')->default(0);
            $table->string('status')->default('ONGOING'); // ONGOING, FINISHED
            
            // Simpan jawaban detail dalam JSON biar hemat tabel
            // Format: { "1": "A", "2": "C" } (question_id: answer)
            $table->json('answers')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('exam_questions');
    }
};