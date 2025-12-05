<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Kita simpan config dalam JSON agar bisa menampung banyak parameter
            // Contoh isi: {"format": "YY-PRODI-SEQ", "separator": "", "seq_digit": 4}
            $table->json('nim_config')->nullable()->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('nim_config');
        });
    }
};