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
        $table->char('id', 26)->primary();
        
        $table->char('user_id', 26);
        // Relasi (Perhatikan tipe datanya harus sama dengan tabel induk)
        $table->char('study_program_id', 26); 

        $table->string('nim', 20)->unique();
        $table->string('entry_year', 4);
        
        // Biodata
        $table->string('pob')->nullable();
        $table->date('dob')->nullable();
        $table->string('phone')->nullable();
        $table->enum('gender', ['L', 'P']);
        
        $table->string('status', 20)->default('active')->index();
        
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
