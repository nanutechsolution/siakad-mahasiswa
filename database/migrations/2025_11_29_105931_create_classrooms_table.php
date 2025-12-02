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
    Schema::create('classrooms', function (Blueprint $table) {
        $table->ulid('id')->primary(); // Pakai ULID biar URL aman
        
        // Relasi Utama
        $table->foreignId('academic_period_id')->constrained(); // Semester Ganjil 2024
        $table->foreignId('course_id')->constrained(); // Matkul Algoritma
        $table->foreignUlid('lecturer_id')->nullable()->constrained(); // Dosen Pengampu (Bisa null kalau belum ditentukan)
        
        // Detail Kelas
        $table->string('name', 5); // Nama Kelas: A, B, C, atau PAGI
        $table->integer('quota')->default(40); // Kapasitas kursi
        $table->integer('enrolled')->default(0); // Jumlah terisi (Cache count)
        
        $table->boolean('is_open')->default(true); // Status kelas dibuka/tutup
        
        // Constraint Unik: Dalam 1 semester, 1 matkul tidak boleh punya nama kelas kembar
        $table->unique(['academic_period_id', 'course_id', 'name']);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
