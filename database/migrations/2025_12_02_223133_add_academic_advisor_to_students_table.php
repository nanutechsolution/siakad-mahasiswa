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
        Schema::table('students', function (Blueprint $table) {
            // Menambahkan kolom dosen wali (bisa null jika belum diset)
            $table->foreignUlid('academic_advisor_id')
                ->nullable()
                ->after('study_program_id')
                ->constrained('lecturers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['academic_advisor_id']);
            $table->dropColumn('academic_advisor_id');
        });
    }
};
