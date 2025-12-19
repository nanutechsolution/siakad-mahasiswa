<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->index(
                ['student_id', 'academic_period_id', 'fee_type_id'],
                'idx_billings_student_period_fee'
            );
        });
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropIndex('idx_billings_student_period_fee');
        });
    }
};
