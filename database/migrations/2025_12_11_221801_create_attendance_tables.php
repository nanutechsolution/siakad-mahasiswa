<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Pertemuan Kelas (Berita Acara)
        Schema::create('class_meetings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('classroom_id')->constrained()->cascadeOnDelete();
            
            $table->integer('meeting_no'); // Pertemuan ke: 1, 2, ... 16
            $table->date('meeting_date');
            $table->text('topic')->nullable(); // Materi yang diajarkan
            $table->boolean('is_open')->default(false); // Apakah sesi absen sedang dibuka?
            $table->string('token', 6)->nullable(); // Kode unik 6 digit untuk mahasiswa absen mandiri
            
            $table->timestamps();
        });

        // Tabel Kehadiran Mahasiswa
        Schema::create('attendances', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('class_meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('student_id')->constrained()->cascadeOnDelete();
            
            // H=Hadir, I=Izin, S=Sakit, A=Alpha
            $table->enum('status', ['H', 'I', 'S', 'A'])->default('A'); 
            $table->timestamp('check_in_at')->nullable(); // Waktu absen
            
            $table->timestamps();

            // Mencegah absen ganda di pertemuan yang sama
            $table->unique(['class_meeting_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('class_meetings');
    }
};