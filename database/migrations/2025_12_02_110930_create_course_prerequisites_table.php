<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_prerequisites', function (Blueprint $table) {
            $table->id();
            // Matkul yang sedang diambil
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            
            // Matkul yang menjadi syarat
            $table->foreignId('prerequisite_id')->constrained('courses')->cascadeOnDelete();
            
            // Nilai minimal untuk dianggap memenuhi syarat (default D)
            $table->string('min_grade', 2)->default('D'); 
            
            $table->timestamps();
            $table->unique(['course_id', 'prerequisite_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_prerequisites');
    }
};