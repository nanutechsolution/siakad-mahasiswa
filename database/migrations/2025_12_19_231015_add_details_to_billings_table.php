<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            // 1. Tambah kolom fee_type_id (Relasi ke Master Jenis Biaya)
            // Menggunakan nullOnDelete agar jika jenis biaya dihapus, history tagihan tidak hilang (hanya jadi null)
            $table->foreignId('fee_type_id')
                ->nullable()
                ->after('academic_period_id')
                ->constrained('fee_types')
                ->nullOnDelete();

            // 2. Tambah kolom tuition_rate_id (Relasi ke Master Tarif)
            // Berguna untuk melacak tagihan ini dibuat berdasarkan aturan tarif yang mana
            $table->foreignId('tuition_rate_id')
                ->nullable()
                ->after('fee_type_id')
                ->constrained('tuition_rates')
                ->nullOnDelete();

            // 3. Tambah kolom semester (Untuk info ini tagihan semester ke-berapa)
            $table->unsignedTinyInteger('semester')
                ->nullable()
                ->after('tuition_rate_id');
        });
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            // Hapus constraint foreign key terlebih dahulu
            $table->dropForeign(['fee_type_id']);
            $table->dropForeign(['tuition_rate_id']);
            
            // Hapus kolom
            $table->dropColumn(['fee_type_id', 'tuition_rate_id', 'semester']);
        });
    }
};