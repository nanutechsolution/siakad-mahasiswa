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
        Schema::table('study_programs', function (Blueprint $table) {
            // Kita buat nullable dulu biar tidak error data lama
            $table->foreignId('faculty_id')->nullable()->after('id')->constrained('faculties')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('study_programs', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->dropColumn('faculty_id');
        });
    }
};
