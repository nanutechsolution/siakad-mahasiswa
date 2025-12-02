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
    Schema::create('students', function (Blueprint $table) {
        $table->ulid('id')->primary();
        
        // Relasi (Perhatikan tipe datanya harus sama dengan tabel induk)
        $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('study_program_id')->constrained();

        $table->string('nim', 20)->unique();
        $table->string('entry_year', 4);
        
        // Biodata
        $table->string('pob')->nullable();
        $table->date('dob')->nullable();
        $table->string('phone')->nullable();
        $table->enum('gender', ['L', 'P']);
        
        $table->char('status', 1)->default('A')->index(); // A=Aktif
        
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
