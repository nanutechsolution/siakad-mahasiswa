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
        Schema::create('courses', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('study_program_id', 26);

            $table->string('code')->unique(); // TI-101
            $table->string('name'); // Algoritma
            $table->string('name_en')->nullable(); // Algorithm (Buat transkrip bilingual)

            $table->integer('semester_default'); // Paket semester berapa (1-8)
            $table->integer('credit_total'); // Total SKS (3)
            $table->integer('credit_theory')->default(0); // SKS Teori
            $table->integer('credit_practice')->default(0); // SKS Praktek

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('study_program_id')
                ->references('id')
                ->on('study_programs')
                ->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
