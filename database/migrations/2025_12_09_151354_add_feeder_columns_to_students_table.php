<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            
            // 1. BIODATA TAMBAHAN
            $table->string('nisn', 20)->nullable()->after('nik');
            $table->string('npwp', 20)->nullable()->after('nisn');
            $table->string('citizenship', 3)->default('ID')->after('npwp'); // ID for Indonesia
            $table->tinyInteger('religion_id')->nullable()->after('citizenship'); // 1=Islam, 2=Kristen, etc (Sesuai Ref Feeder)
             $table->string('email')->nullable()->after('religion_id');
            $table->boolean('is_kps_recipient')->default(false)->after('email'); // Penerima KPS?
            $table->string('kps_number')->nullable()->after('is_kps_recipient');

            // 2. ALAMAT RINCI
            // 'address' (Jalan) sudah ada
            $table->string('dusun')->nullable()->after('address');
            $table->string('rt', 5)->nullable()->after('dusun');
            $table->string('rw', 5)->nullable()->after('rt');
            $table->string('kelurahan')->nullable()->after('rw');
            $table->string('postal_code', 10)->nullable()->after('kelurahan');
            $table->bigInteger('region_id')->nullable()->after('postal_code'); // ID Wilayah (Kode Kemendagri)
            $table->tinyInteger('residence_type_id')->nullable()->after('region_id'); // Jenis Tinggal (Kos, Ortu, dll)
            $table->tinyInteger('transportation_id')->nullable()->after('residence_type_id'); // Alat Transportasi

            // 3. DATA AYAH
            $table->string('father_nik', 20)->nullable()->after('status');
            $table->string('father_name')->nullable()->after('father_nik');
            $table->date('father_dob')->nullable()->after('father_name');
            $table->tinyInteger('father_education_id')->nullable()->after('father_dob');
            $table->tinyInteger('father_occupation_id')->nullable()->after('father_education_id');
            $table->tinyInteger('father_income_id')->nullable()->after('father_occupation_id');

            // 4. DATA IBU (Sangat Penting utk Validasi)
            $table->string('mother_nik', 20)->nullable()->after('father_income_id');
            $table->string('mother_name')->nullable()->after('mother_nik'); // Ibu Kandung
            $table->date('mother_dob')->nullable()->after('mother_name');
            $table->tinyInteger('mother_education_id')->nullable()->after('mother_dob');
            $table->tinyInteger('mother_occupation_id')->nullable()->after('mother_education_id');
            $table->tinyInteger('mother_income_id')->nullable()->after('mother_occupation_id');

            // 5. DATA WALI (Opsional)
            $table->string('guardian_name')->nullable()->after('mother_income_id');
            $table->date('guardian_dob')->nullable()->after('guardian_name');
            $table->tinyInteger('guardian_education_id')->nullable()->after('guardian_dob');
            $table->tinyInteger('guardian_occupation_id')->nullable()->after('guardian_education_id');
            $table->tinyInteger('guardian_income_id')->nullable()->after('guardian_occupation_id');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'nisn', 'npwp', 'citizenship', 'religion_id', 'email',
                'is_kps_recipient', 'kps_number',
                'dusun', 'rt', 'rw', 'kelurahan', 'postal_code', 'region_id', 'residence_type_id', 'transportation_id',
                'father_nik', 'father_name', 'father_dob', 'father_education_id', 'father_occupation_id', 'father_income_id',
                'mother_nik', 'mother_name', 'mother_dob', 'mother_education_id', 'mother_occupation_id', 'mother_income_id',
                'guardian_name', 'guardian_dob', 'guardian_education_id', 'guardian_occupation_id', 'guardian_income_id'
            ]);
        });
    }
};