<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edom_questions', function (Blueprint $table) {
            $table->id();
            // Kategori: Pedagogik, Profesional, Kepribadian, Sosial
            $table->string('category', 50); 
            $table->text('question_text'); // Soal: "Dosen mengajar tepat waktu"
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edom_questions');
    }
};