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
        Schema::table('courses', function (Blueprint $table) {
            // Kelompok Matkul: MKU (Umum), MKDK (Dasar Keahlian), MKK (Keahlian Utama), MKP (Pilihan)
            $table->string('group_code', 10)->default('MKK')->after('name');

            // Apakah Wajib? (Untuk fitur generate paket)
            $table->boolean('is_mandatory')->default(true)->after('group_code');

            // Link RPS (Silabus PDF)
            $table->string('syllabus_path')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            //
        });
    }
};
