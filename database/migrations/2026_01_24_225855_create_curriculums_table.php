<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('curriculums', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('study_program_id', 26);

            $table->string('name');
            $table->year('year');
            $table->integer('total_courses')->default(0);
            $table->integer('total_credits')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['study_program_id', 'year']);
            $table->foreign('study_program_id')
                ->references('id')
                ->on('study_programs')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculums');
    }
};
