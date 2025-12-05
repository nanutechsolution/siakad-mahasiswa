<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            
            // Siapa pelakunya?
            $table->foreignUlid('user_id')->nullable()->constrained()->nullOnDelete();
            
            // Apa aksinya? (Created, Updated, Deleted, Login, Logout)
            $table->string('action'); 
            
            // Pada objek apa? (Misal: Student #123)
            $table->string('subject_type')->nullable(); // App\Models\Student
            $table->string('subject_id')->nullable();   // ID Student
            
            // Detail (Opsional, misal perubahan data lama -> baru)
            $table->text('description')->nullable(); 
            
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};