<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            // Kita gunakan foreignUlid karena tabel registrants menggunakan primary key ULID
            // Dibuat nullable karena tagihan mahasiswa lama tidak punya registrant_id
            $table->foreignUlid('registrant_id')
                ->nullable()
                ->after('student_id')
                ->constrained()
                ->nullOnDelete();

            // Opsional: Karena sekarang student_id bisa kosong (untuk camaba), 
            // kita harus memastikan kolom student_id di migrasi lama dibuat nullable.
            $table->foreignUlid('student_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropForeign(['registrant_id']);
            $table->dropColumn('registrant_id');
            $table->foreignUlid('student_id')->nullable(false)->change();
        });
    }
};