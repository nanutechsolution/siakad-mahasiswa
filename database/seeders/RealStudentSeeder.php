<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\StudyProgram;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RealStudentSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('csv/mahasiswa_real.csv');

        if (!file_exists($csvFile)) {
            $this->command->error("File tidak ditemukan di: $csvFile");
            return;
        }

        $this->command->info('Mulai import data mahasiswa dari CSV...');

        // Cache Prodi ID (Key: lowercase nama prodi)
        $prodis = StudyProgram::all()->pluck('id', 'name')->mapWithKeys(function ($item, $key) {
            return [strtolower($key) => $item];
        });

        $file = fopen($csvFile, 'r');
        $row = 0;

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($file, 2000, ",")) !== FALSE) {
                $row++;
                
                // Skip Header (9 Baris pertama)
                if ($row < 10) continue;

                // Mapping Kolom (Sesuai CSV Anda)
                // 1: NIM
                // 2: NIK
                // 3: Nama
                // 4: Prodi
                // 5: Tanggal Masuk (25 August 2025) -> Ambil Tahunnya Saja!
                // 6: Tahun Masuk (Kadang kosong atau format salah)
                // 10: TTL
                // 12: Alamat

                $nim = trim($data[1] ?? '');
                if (empty($nim)) continue; // Skip baris kosong

                $fullName = trim($data[3] ?? '');
                $prodiRaw = trim($data[4] ?? '');
                
                // --- PERBAIKAN UTAMA: ENTRY YEAR ---
                $entryYear = date('Y'); // Default tahun ini
                
                // Coba ambil dari kolom 6 (Tahun Masuk) dulu
                if (!empty($data[6]) && is_numeric($data[6]) && strlen($data[6]) == 4) {
                    $entryYear = $data[6];
                } 
                // Jika gagal, coba ambil dari kolom 5 (Tanggal Masuk: "25 August 2025")
                elseif (!empty($data[5])) {
                    try {
                        // Coba parse tanggal "25 August 2025"
                        $date = Carbon::parse($data[5]);
                        $entryYear = $date->year;
                    } catch (\Exception $e) {
                        // Jika gagal parse, ambil 4 digit terakhir string
                        // Misal "2025" dari string acak
                        if (preg_match('/\d{4}/', $data[5], $matches)) {
                            $entryYear = $matches[0];
                        }
                    }
                }
                // ----------------------------------

                $nik = $this->cleanNumber($data[2] ?? '');
                
                // Cari Prodi
                $cleanProdiName = trim(str_replace(['S1 ', 'D3 '], '', $prodiRaw));
                $prodiId = $prodis[strtolower($cleanProdiName)] ?? null;
                
                if (!$prodiId) {
                    // Fallback cari manual
                    $prodiLike = StudyProgram::where('name', 'like', '%' . $cleanProdiName . '%')->first();
                    $prodiId = $prodiLike ? $prodiLike->id : 1;
                }

                // Gender (Kolom ke-9 atau 10)
                // Di CSV Anda, gender ada di kolom index 9 (setelah Biaya Masuk)
                $genderRaw = trim($data[9] ?? 'L'); 
                $gender = (strtoupper($genderRaw) == 'P') ? 'P' : 'L';

                // TTL Parsing
                $ttlRaw = $data[10] ?? ''; 
                $dob = null;
                $pob = null;
                if (strpos($ttlRaw, ',') !== false) {
                    $parts = explode(',', $ttlRaw);
                    $pob = trim($parts[0]);
                    try {
                        $dobString = trim($parts[1]);
                        $dob = Carbon::parse($dobString)->format('Y-m-d');
                    } catch (\Exception $e) { $dob = null; }
                }

                // Buat User
                $email = strtolower($nim) . '@student.unmaris.ac.id';
                $user = User::firstOrCreate(
                    ['username' => $nim], 
                    [
                        'name' => $fullName,
                        'email' => $email,
                        'password' => Hash::make($nim), // Password = NIM
                        'role' => 'student',
                        'email_verified_at' => now(),
                    ]
                );

                // Buat Student
                Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'study_program_id' => $prodiId,
                        'nim' => $nim,
                        'entry_year' => (string) $entryYear, // Pakai tahun yang sudah diclean
                        'pob' => $pob,
                        'dob' => $dob,
                        'gender' => $gender,
                        'address' => $data[12] ?? null,
                        'status' => 'A',
                        'is_new_student' => true
                    ]
                );

                $this->command->info("Imported: $nim ($entryYear)");
            }

            DB::commit();
            $this->command->info("SELESAI! Data mahasiswa berhasil diimport.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error pada baris $row: " . $e->getMessage());
        }

        fclose($file);
    }

    private function cleanNumber($value)
    {
        if (empty($value)) return null;
        $value = trim($value);
        if (strpos(strtoupper($value), 'E') !== false) {
            $value = number_format((float)$value, 0, '', '');
        }
        return str_replace('.0', '', $value);
    }
}