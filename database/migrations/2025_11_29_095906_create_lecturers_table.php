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
            $table->char('id', 26)->primary(); // Pastikan Primary Key juga ULID
            $table->char('user_id', 26); // Relasi ke Users
            $table->char('study_program_id', 26)->nullable();
            $table->string('nidn')->nullable()->unique();
            $table->string('nip_internal')->nullable()->unique();
            $table->string('front_title')->nullable();
            $table->string('back_title')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreign('study_program_id')
                ->references('id')
                ->on('study_programs')
                ->onDelete('set null'); // Jika prodi hapus, dosen jangan hilang

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
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
