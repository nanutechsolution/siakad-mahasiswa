<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('curriculum_courses', function (Blueprint $table) {
            $table->char('id', 26)->primary();

            $table->char('curriculum_id', 26);
            // Relasi utama
            $table->foreign('curriculum_id')
                ->references('id')
                ->on('curriculums')
                ->onDelete('cascade');

            $table->char('course_id', 26);
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            $table->char('course_group_id', 26);
            // Akademik
            $table->unsignedTinyInteger('semester');

            $table->foreign('course_group_id')
                ->references('id')
                ->on('course_groups')
                ->onDelete('restrict');

            $table->boolean('is_mandatory')->default(true);

            // SKS per kurikulum
            $table->unsignedTinyInteger('credit_total');
            $table->unsignedTinyInteger('credit_theory')->default(0);
            $table->unsignedTinyInteger('credit_practice')->default(0);

            $table->timestamps();

            // Satu matkul tidak boleh dobel di kurikulum yang sama
            $table->unique(['curriculum_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_courses');
    }
};
