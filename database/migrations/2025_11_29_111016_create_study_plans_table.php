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
        Schema::create('study_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('student_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_period_id')->constrained(); // Biar cepat query history

            // Status KRS
            $table->enum('status', ['DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED'])->default('DRAFT');

            // Tempat Nilai (Nanti diisi Dosen)
            $table->float('score_number')->nullable(); // 0-100
            $table->char('grade_letter', 2)->nullable(); // A, B+, C
            $table->float('grade_point')->nullable(); // 4.0, 3.5

            $table->timestamps();

            // Mencegah duplikasi: Mahasiswa tidak bisa ambil kelas yang sama 2x di semester yg sama
            $table->unique(['student_id', 'classroom_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_plans');
    }
};
