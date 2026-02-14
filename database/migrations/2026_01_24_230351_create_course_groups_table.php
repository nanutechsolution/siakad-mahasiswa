<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('course_groups');

        Schema::create('course_groups', function (Blueprint $table) {
            // UBAH DARI ID() MENJADI CHAR(26) ULID
            $table->char('id', 26)->primary();
            
            $table->string('code', 20)->unique(); // MKK, MKB
            $table->string('name');
            $table->text('description')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_groups');
    }
};