<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Hapus tabel lama jika ada
        Schema::dropIfExists('academic_periods');

        Schema::create('academic_periods', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID (Wajib Ganti)
            
            $table->string('code', 10)->unique(); // 20241, 20242
            $table->string('name'); // Semester Ganjil 2024/2025
            
            $table->date('start_date');
            $table->date('end_date');
            
            // Status boolean (tinyint)
            $table->boolean('is_active')->default(false);
            $table->boolean('allow_krs')->default(false);
            $table->boolean('allow_input_score')->default(false);
            
            $table->timestamps();
            
            // Indexing untuk performa filter
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_periods');
    }
};