<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Student;
use App\Models\StudyProgram;

class NimGeneratorService
{
    /**
     * Generate NIM Baru (Untuk User Real)
     */
    public function generate($prodiId, $entryYear)
    {
        $setting = Setting::first();
        $config = $setting->nim_config ?? $this->defaults();

        $prodi = StudyProgram::find($prodiId);

        if (!$prodi) return 'ERR-PRODI';

        return $this->buildNimString($config, $entryYear, $prodi, null);
    }

    /**
     * Generate Preview
     */
    public function preview($config, $prodi = null)
    {
        $year = date('Y');

        if (!$prodi) {
            $prodi = new StudyProgram();
            $prodi->code = 'TI';
            $prodi->id = 1;
        }

        return $this->buildNimString($config, $year, $prodi, 1);
    }

    /**
     * Core Logic Pembentuk String NIM
     */
    private function buildNimString($config, $year, $prodi, $sequence = null)
    {
        // 1. Tahun (2 Digit atau 4 Digit)
        $yearStr = ($config['year_format'] == 'YYYY') ? $year : substr($year, -2);

        // 2. Kode Prodi (CUSTOM MAPPING)
        // Ambil dari array 'prodi_codes' di config. Key-nya adalah ID Prodi.
        // Jika tidak ada settingan, fallback ke Kode Huruf default (misal: TI)
        $customCode = $config['prodi_codes'][$prodi->id] ?? $prodi->code;

        $prodiStr = $customCode;

        // 3. Nomor Urut
        if ($sequence === null) {
            $lastStudentCount = Student::where('study_program_id', $prodi->id)
                ->where('entry_year', $year)
                ->count();
            $sequence = $lastStudentCount + 1;
        }

        $seqStr = str_pad($sequence, $config['seq_digit'], '0', STR_PAD_LEFT);

        // FORMAT: TAHUN + KODE_PRODI + URUT
        return $yearStr . $prodiStr . $seqStr;
    }

    private function defaults()
    {
        return [
            'year_format' => 'YY',
            'prodi_codes' => [], // Array kosong default
            'seq_digit' => 4,
        ];
    }
}
