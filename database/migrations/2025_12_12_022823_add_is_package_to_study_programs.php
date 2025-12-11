<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('study_programs', function (Blueprint $table) {
            // Default false (Sistem SKS Biasa)
            // Jika true, berarti Full Paket Sem 1-8
            $table->boolean('is_package')->default(false)->after('degree');
        });
    }

    public function down(): void
    {
        Schema::table('study_programs', function (Blueprint $table) {
            $table->dropColumn('is_package');
        });
    }
};