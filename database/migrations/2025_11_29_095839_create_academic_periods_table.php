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
        Schema::create('academic_periods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // 20241
            $table->string('name'); // Ganjil 2024/2025
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false)->index();
            $table->boolean('allow_krs')->default(false);
            $table->boolean('allow_input_score')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_periods');
    }
};
