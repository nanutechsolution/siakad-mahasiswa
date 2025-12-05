<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Student;
use App\Models\StudyProgram;

class NimGeneratorService
{
    /**
     * Generate NIM Baru
     * @param int $prodiId ID Program Studi
     * @param string $entryYear Tahun Masuk (2025)
     */
    public function generate($prodiId, $entryYear)
    {
        $setting = Setting::first();
        $config = $setting->nim_config ?? $this->defaults();

        $prodi = StudyProgram::find($prodiId);
        
        // 1. Tentukan Format Tahun
        // "YY" (25) atau "YYYY" (2025)
        $yearStr = ($config['year_format'] == 'YYYY') ? $entryYear : substr($entryYear, -2);

        // 2. Tentukan Kode Prodi
        // Bisa pakai ID (1, 2) atau Kode String (TI, SI)
        $prodiStr = ($config['prodi_source'] == 'CODE') ? $prodi->code : str_pad($prodi->id, 2, '0', STR_PAD_LEFT);

        // 3. Hitung Urutan (Sequence)
        // Hitung mahasiswa di prodi & angkatan yg sama
        $lastStudentCount = Student::where('study_program_id', $prodiId)
            ->where('entry_year', $entryYear)
            ->count();
        
        $nextSequence = $lastStudentCount + 1;
        $seqStr = str_pad($nextSequence, $config['seq_digit'], '0', STR_PAD_LEFT);

        // 4. Rakit String (Pola: TAHUN + PRODI + URUT)
        // Anda bisa ubah urutan ini jika config mengizinkan, tapi standar biasanya begini
        return $yearStr . $prodiStr . $seqStr;
    }

    /**
     * Generate Preview untuk tampilan Admin
     */
    public function preview($config)
    {
        $year = date('Y');
        $yearStr = ($config['year_format'] == 'YYYY') ? $year : substr($year, -2);
        $prodiStr = ($config['prodi_source'] == 'CODE') ? 'TI' : '01';
        $seqStr = str_pad('1', $config['seq_digit'], '0', STR_PAD_LEFT);

        return $yearStr . $prodiStr . $seqStr;
    }

    private function defaults()
    {
        return [
            'year_format' => 'YY', // YY = 25, YYYY = 2025
            'prodi_source' => 'CODE', // CODE = TI, ID = 01
            'seq_digit' => 4, // 0001
        ];
    }
}