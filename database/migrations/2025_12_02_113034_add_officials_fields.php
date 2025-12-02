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
        // 1. Tambah Pejabat Teras di Tabel Settings
        Schema::table('settings', function (Blueprint $table) {
            $table->string('foundation_name')->nullable()->after('campus_name'); // Nama Yayasan
            $table->string('foundation_head')->nullable()->after('foundation_name'); // Ketua Yayasan
            $table->string('rector_name')->nullable()->after('foundation_head'); // Rektor
            $table->string('rector_nip')->nullable()->after('rector_name'); // NIP Rektor
        });

        // 2. Tambah Kaprodi di Tabel Study Programs
        Schema::table('study_programs', function (Blueprint $table) {
            $table->string('head_name')->nullable()->after('name'); // Nama Kaprodi
            $table->string('head_nip')->nullable()->after('head_name'); // NIP/NIDN Kaprodi
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['foundation_name', 'foundation_head', 'rector_name', 'rector_nip']);
        });

        Schema::table('study_programs', function (Blueprint $table) {
            $table->dropColumn(['head_name', 'head_nip']);
        });
    }
};
