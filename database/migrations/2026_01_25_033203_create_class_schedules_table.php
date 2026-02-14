<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabel ini menyimpan Waktu & Tempat untuk sebuah Kelas
        // Satu Kelas (course_classes) bisa punya banyak jadwal (Senin & Kamis)
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->char('id', 26)->primary(); // ULID
            
            $table->char('course_class_id', 26); // Link ke Kelas (Algoritma - A)
            
            // 1=Senin, 2=Selasa, ..., 7=Minggu
            $table->tinyInteger('day_of_week'); 
            
            $table->time('start_time'); // 08:00
            $table->time('end_time');   // 10:00
            
            // Jika punya tabel master 'rooms', ganti jadi char(26) room_id
            // Untuk sekarang pakai string dulu biar aman
            $table->string('room_name', 50)->nullable(); // Lab Komputer 1, R.204
            
            // Jenis Pertemuan (Opsional)
            $table->enum('type', ['theory', 'practice', 'response'])->default('theory');
            
            $table->timestamps();

            // Aturan Unik: Tidak boleh ada kelas di Ruangan Sama pada Jam yang Sama (Cegah Bentrok Ruangan)
            // Note: Validasi ini kompleks di database, sebaiknya divalidasi juga di Logic PHP (Laravel)
            
            $table->foreign('course_class_id')
                  ->references('id')
                  ->on('course_classes')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};