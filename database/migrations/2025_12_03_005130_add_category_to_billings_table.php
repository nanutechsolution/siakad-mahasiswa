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
        Schema::table('billings', function (Blueprint $table) {
            // Tambahkan kategori setelah kolom title
            $table->string('category', 20)->default('SPP')->after('title');
            // Opsional: Bisa pakai ENUM jika jenisnya pasti:
            // $table->enum('category', ['SPP', 'GEDUNG', 'SERAGAM', 'WISUDA', 'LAINNYA'])->default('SPP');
        });
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
