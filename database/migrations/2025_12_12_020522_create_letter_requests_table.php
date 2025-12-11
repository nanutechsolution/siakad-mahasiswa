<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_requests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('student_id')->constrained()->cascadeOnDelete();
            
            // Jenis Surat: AKTIF_KULIAH, MAGANG, CUTI, PENELITIAN
            $table->string('type', 50); 
            $table->text('purpose'); // Keperluan: "Untuk Tunjangan Gaji Ortu"
            
            // Status: PENDING, PROCESSED, REJECTED, COMPLETED
            $table->enum('status', ['PENDING', 'PROCESSED', 'REJECTED', 'COMPLETED'])->default('PENDING');
            
            // Diisi Admin saat proses
            $table->string('letter_number')->nullable(); // Nomor Surat Resmi
            $table->string('file_path')->nullable(); // Jika upload surat manual
            $table->text('admin_note')->nullable(); // Catatan penolakan/info
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_requests');
    }
};