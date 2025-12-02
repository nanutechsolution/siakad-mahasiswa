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
        Schema::create('structure_histories', function (Blueprint $table) {
            $table->id();

            // Kolom ini akan menampung ID dari Fakultas/Prodi/Setting
            // structurable_type = 'App\Models\Faculty'
            // structurable_id = 1
            $table->morphs('structurable');

            $table->string('position'); // Rektor, Dekan, Kaprodi, Ketua Yayasan
            $table->string('official_name'); // Nama Pejabat
            $table->string('official_nip')->nullable();

            $table->date('start_date'); // Tanggal Menjabat
            $table->date('end_date')->nullable(); // Tanggal Selesai (Null = Masih Menjabat)
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('structure_histories');
    }
};
