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
        Schema::create('lecturers', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('study_program_id')->nullable()->constrained();

            $table->string('nidn')->nullable()->unique();
            $table->string('nip_internal')->nullable()->unique();
            $table->string('front_title')->nullable();
            $table->string('back_title')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturers');
    }
};
