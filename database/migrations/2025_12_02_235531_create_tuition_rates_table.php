<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tuition_rates', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('study_program_id', 26);
            // UBAH DARI STRING KE RELASI
            $table->foreignId('fee_type_id')->constrained('fee_types')->cascadeOnDelete();

            $table->year('entry_year');
            $table->decimal('amount', 15, 2);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Mencegah duplikat: 1 Prodi + 1 Tahun + 1 Jenis Biaya hanya boleh 1 tarif
            $table->unique(['study_program_id', 'entry_year', 'fee_type_id'], 'unique_rate');
            $table->foreign('study_program_id')
                ->references('id')
                ->on('study_programs')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tuition_rates');
    }
};
