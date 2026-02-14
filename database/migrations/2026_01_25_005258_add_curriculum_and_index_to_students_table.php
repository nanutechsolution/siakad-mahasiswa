<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // 1. Buat kolom harus nullable dulu (karena mhs lama mungkin belum punya kurikulum)
            // PENTING: Tipe data harus sama dengan id di tabel curriculums (CHAR 26)
            $table->char('curriculum_id', 26)->nullable()->after('study_program_id');

            // 2. Index untuk performa Query KRS
            $table->index('curriculum_id');
            // 3. Foreign Key (Menjaga integritas data)
            // Jika kurikulum dihapus, set null (jangan hapus mahasiswanya!)
            $table->foreign('curriculum_id')
                ->references('id')
                ->on('curriculums')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            // Hapus FK dulu baru kolom
            $table->dropForeign(['curriculum_id']);
            $table->dropColumn('curriculum_id');
        });
    }
};
