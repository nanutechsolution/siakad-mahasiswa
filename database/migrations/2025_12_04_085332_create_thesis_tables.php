<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Header Skripsi (Proposal)
        Schema::create('theses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_period_id')->constrained(); // Semester pengajuan
            
            $table->string('title'); // Judul Skripsi
            $table->text('abstract')->nullable();
            $table->string('proposal_file')->nullable(); // File PDF Proposal
            
            // Status: PROPOSED (Diajukan), APPROVED (Disetujui Kaprodi), REVISION, COMPLETED (Lulus Sidang)
            $table->enum('status', ['PROPOSED', 'REJECTED', 'APPROVED', 'ON_PROGRESS', 'COMPLETED'])->default('PROPOSED');
            
            $table->timestamps();
        });

        // 2. Tabel Dosen Pembimbing (Relasi Many-to-Many dengan role)
        Schema::create('thesis_supervisors', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('thesis_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('lecturer_id')->constrained()->cascadeOnDelete();
            
            // Role: 1 (Pembimbing Utama), 2 (Pembimbing Pendamping)
            $table->tinyInteger('role')->default(1); 
            
            // Status: Apakah dosen bersedia?
            $table->enum('status', ['PENDING', 'ACCEPTED', 'DECLINED'])->default('PENDING');
            
            $table->timestamps();
            $table->unique(['thesis_id', 'lecturer_id']); // Satu dosen ga boleh dobel di satu skripsi
        });

        // 3. Tabel Log Bimbingan (Kartu Kontrol)
        Schema::create('thesis_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('thesis_id')->constrained()->cascadeOnDelete();
            
            $table->date('guidance_date'); // Tanggal Bimbingan
            $table->text('notes'); // Catatan revisi/arahan
            $table->text('student_notes')->nullable(); // Catatan dari mahasiswa
            $table->string('file_attachment')->nullable(); // File revisi (bab 1, dll)
            
            // Validasi Dosen: DRAFT (Mhs tulis), APPROVED (Dosen memvalidasi/paraf digital)
            $table->enum('status', ['DRAFT', 'APPROVED'])->default('DRAFT');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thesis_logs');
        Schema::dropIfExists('thesis_supervisors');
        Schema::dropIfExists('theses');
    }
};