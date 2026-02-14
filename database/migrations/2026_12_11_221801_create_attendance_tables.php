<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Pertemuan Kelas (Berita Acara / Jurnal Kuliah)
        Schema::create('class_meetings', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            
            // FIX: Ubah classroom_id menjadi course_class_id
            $table->char('course_class_id', 26); 
            
            $table->integer('meeting_no'); // Pertemuan ke: 1, 2, ... 16
            $table->date('meeting_date');
            $table->text('topic')->nullable(); // Materi yang diajarkan
            $table->boolean('is_open')->default(false); // Sesi absen dibuka?
            $table->string('token', 6)->nullable(); // Token absen mandiri
            
            $table->timestamps();

            // Relasi ke course_classes
            $table->foreign('course_class_id')
                  ->references('id')
                  ->on('course_classes')
                  ->onDelete('cascade');
        });

        // 2. Tabel Kehadiran Mahasiswa
        Schema::create('attendances', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            
            $table->char('class_meeting_id', 26);
            $table->char('student_id', 26);
            
            // H=Hadir, I=Izin, S=Sakit, A=Alpha
            $table->enum('status', ['H', 'I', 'S', 'A'])->default('A'); 
            $table->timestamp('check_in_at')->nullable(); // Waktu tap absen
            $table->string('ip_address')->nullable(); // Opsional: Cek lokasi
            $table->string('device_info')->nullable(); // Opsional: Cek HP ganti-ganti
            
            $table->timestamps();

            // Mencegah absen ganda di pertemuan yang sama
            $table->unique(['class_meeting_id', 'student_id']);

            // Foreign Keys
            $table->foreign('class_meeting_id')
                  ->references('id')
                  ->on('class_meetings')
                  ->onDelete('cascade');

            $table->foreign('student_id')
                  ->references('id')
                  ->on('students')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('class_meetings');
    }
};