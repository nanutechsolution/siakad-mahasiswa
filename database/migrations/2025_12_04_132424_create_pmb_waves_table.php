<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pmb_waves', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Gelombang 1, Jalur Prestasi
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false); // Switch manual on/off
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pmb_waves');
    }
};