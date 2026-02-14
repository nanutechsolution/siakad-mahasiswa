<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Header Skripsi (Proposal & TA)
        Schema::create('theses', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            
            // Relasi ke Mahasiswa & Periode
            $table->char('student_id', 26);
            $table->char('academic_period_id', 26); // FIX: Ubah ke Char 26 agar jodoh
            
            $table->string('title'); // Judul Skripsi
            $table->text('abstract')->nullable();
            $table->string('proposal_file')->nullable(); // File PDF Proposal
            
            // Status Flow Skripsi
            $table->enum('status', ['PROPOSED', 'REJECTED', 'APPROVED', 'ON_PROGRESS', 'COMPLETED'])->default('PROPOSED');
            
            $table->timestamps();

            // Foreign Keys
            $table->foreign('student_id')
                  ->references('id')
                  ->on('students')
                  ->onDelete('cascade');

            $table->foreign('academic_period_id')
                  ->references('id')
                  ->on('academic_periods')
                  ->onDelete('cascade');
        });

        // 2. Tabel Dosen Pembimbing (Relasi Many-to-Many dengan role)
        Schema::create('thesis_supervisors', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            
            $table->char('thesis_id', 26);
            $table->char('lecturer_id', 26);
            
            // Role: 1 (Pembimbing Utama), 2 (Pembimbing Pendamping)
            $table->tinyInteger('role')->default(1); 
            
            // Status: Apakah dosen bersedia?
            $table->enum('status', ['PENDING', 'ACCEPTED', 'DECLINED'])->default('PENDING');
            
            $table->timestamps();

            // Unique Constraint: Satu dosen tidak boleh dobel di satu skripsi
            $table->unique(['thesis_id', 'lecturer_id']); 

            // Foreign Keys
            $table->foreign('thesis_id')
                  ->references('id')
                  ->on('theses')
                  ->onDelete('cascade');

            $table->foreign('lecturer_id')
                  ->references('id')
                  ->on('lecturers')
                  ->onDelete('cascade');
        });

        // 3. Tabel Log Bimbingan (Kartu Kontrol)
        Schema::create('thesis_logs', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            
            $table->char('thesis_id', 26);
            
            $table->date('guidance_date'); // Tanggal Bimbingan
            $table->text('notes'); // Catatan revisi/arahan
            $table->text('student_notes')->nullable(); // Catatan dari mahasiswa
            $table->string('file_attachment')->nullable(); // File revisi (bab 1, dll)
            
            // Validasi Dosen: DRAFT (Mhs tulis), APPROVED (Dosen memvalidasi/paraf digital)
            $table->enum('status', ['DRAFT', 'APPROVED'])->default('DRAFT');
            
            $table->timestamps();

            // Foreign Keys
            $table->foreign('thesis_id')
                  ->references('id')
                  ->on('theses')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thesis_logs');
        Schema::dropIfExists('thesis_supervisors');
        Schema::dropIfExists('theses');
    }
};