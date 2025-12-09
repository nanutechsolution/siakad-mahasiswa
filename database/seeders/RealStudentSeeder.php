<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\StudyProgram;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RealStudentSeeder extends Seeder
{
    public function run(): void
{
    // 1. Lokasi File CSV
    $csvFile = database_path('csv/mahasiswa_real.csv');

    if (!file_exists($csvFile)) {
        $this->command->error("File tidak ditemukan di: $csvFile");
        $this->command->info("Harap rename file CSV Anda menjadi 'mahasiswa_real.csv' dan letakkan di folder 'database/csv/'");
        return;
    }

    $this->command->info('Mulai import data mahasiswa...');

    // 2. Cache Prodi ID untuk performa (Key: nama prodi lowercase)
    // Kita ambil semua prodi yang ada di DB
    $prodis = StudyProgram::all();

    // Buka File
    $file = fopen($csvFile, 'r');
    $row = 0;

    // Gunakan Transaksi DB biar ngebut dan aman
    DB::beginTransaction();
    
    try {
        $importedCount = 0; // Counter untuk track jumlah yang berhasil diimport

        while (($data = fgetcsv($file, 2000, ",")) !== FALSE) {
            $row++;

            // SKIP HEADER (9 Baris pertama di file Anda adalah kop surat)
            if ($row < 10) continue;

            // MAPPING KOLOM (Berdasarkan file DB Mahasiswa Aktif 2025.csv)
            // Index 1: NIM
            // Index 2: NIK (Sering error E+15)
            // Index 3: Nama Lengkap
            // Index 4: Program Studi
            // Index 5: Tanggal Masuk (Format: "25 August 2025")
            // Index 6: Tahun Masuk (Bisa kosong atau angka 2025)
            // Index 9: Gender (L/P)
            // Index 10: TTL (Format: "KOTA, DD Month YYYY")
            // Index 12: Alamat

            $nim = trim($data[1] ?? '');
            
            // Skip baris jika NIM kosong (baris footer/kosong)
            if (empty($nim)) continue;

            $fullName = trim($data[3] ?? '');

            // --- 1. BERSIHKAN NIK (Scientific Notation Fix) ---
            $nikRaw = $data[2] ?? '';
            $nik = $this->cleanNumber($nikRaw);

            // --- 2. FIX TAHUN MASUK (Penyebab Error Sebelumnya) ---
            $entryYear = date('Y'); // Default tahun ini
            
            // Prioritas 1: Ambil dari kolom 6 (Tahun Masuk) jika valid 4 digit
            if (!empty($data[6]) && is_numeric($data[6]) && strlen($data[6]) == 4) {
                $entryYear = $data[6];
            } 
            // Prioritas 2: Ambil dari kolom 5 (Tanggal Masuk) jika ada
            elseif (!empty($data[5])) {
                try {
                    // Coba parse "25 August 2025"
                    $date = Carbon::parse($data[5]);
                    $entryYear = $date->year;
                } catch (\Exception $e) {
                    // Jika gagal parse, cari 4 digit angka (regex)
                    if (preg_match('/\d{4}/', $data[5], $matches)) {
                        $entryYear = $matches[0];
                    }
                }
            }
            // Pastikan entryYear dipotong max 4 karakter agar muat di database
            $entryYear = substr((string)$entryYear, 0, 4);

            // --- 3. CARI PRODI ID ---
            $prodiRaw = trim($data[4] ?? '');
            // Bersihkan kata-kata umum agar pencarian lebih akurat
            // Hapus: "S1", "D3", "Pendidikan" (agar "S1 Pendidikan Teknologi Informasi" match dengan "Teknologi Informasi" atau sejenisnya)
            $cleanProdiName = trim(str_ireplace(['S1', 'D3', 'Pendidikan'], '', $prodiRaw));
            
            $prodiId = null;
            
            // Loop cari yang paling mirip di database
            foreach ($prodis as $p) {
                // Cek apakah nama prodi di DB ada di dalam string CSV (atau sebaliknya)
                if (stripos($prodiRaw, $p->name) !== false || stripos($p->name, $cleanProdiName) !== false) {
                    $prodiId = $p->id;
                    break;
                }
            }
            
            // Fallback: Jika tidak ketemu, pakai ID 1 (Daripada error)
            if (!$prodiId) {
                $prodiId = $prodis->first()->id ?? 1;
                // Uncomment baris bawah jika ingin debug prodi yang tidak ketemu
                // $this->command->warn("Prodi tidak match: '$prodiRaw'. Default ke ID: $prodiId");
            }

            // --- 4. PARSING GENDER & TTL ---
            $genderRaw = trim($data[9] ?? 'L'); 
            $gender = (strtoupper($genderRaw) == 'P') ? 'P' : 'L';

            $ttlRaw = $data[10] ?? ''; 
            $dob = null;
            $pob = null;

            if (strpos($ttlRaw, ',') !== false) {
                $parts = explode(',', $ttlRaw);
                $pob = trim($parts[0]); // Tempat Lahir
                try {
                    // Parse tanggal "30 August 1996"
                    $dobString = trim($parts[1] ?? '');
                    if($dobString) {
                        $dob = Carbon::parse($dobString)->format('Y-m-d');
                    }
                } catch (\Exception $e) { $dob = null; }
            } else {
                $pob = $ttlRaw; 
            }

            // --- 5. BUAT USER LOGIN ---
            // Username = NIM, Password = NIM
            $email = strtolower($nim) . '@student.unmaris.ac.id';

            $user = User::firstOrCreate(
                ['username' => $nim], 
                [
                    'name' => $fullName,
                    'email' => $email,
                    'password' => Hash::make($nim), // Password default = NIM
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]
            );

            // --- 6. BUAT DATA MAHASISWA ---
            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'study_program_id' => $prodiId,
                    'nim' => $nim,
                    'entry_year' => $entryYear,
                    'pob' => $pob,
                    'dob' => $dob,
                    'gender' => $gender,
                    'address' => $data[12] ?? null, // Alamat
                    'status' => 'A', // Default Aktif
                    'is_new_student' => true // Agar muncul onboarding saat login pertama
                ]
            );

            $this->command->info("Imported: $nim ($entryYear) - $fullName");

            $importedCount++; // Increment counter

            // Stop after importing 10 records
            if ($importedCount >= 10) {
                break;
            }
        }

        DB::commit();
        $this->command->info("SELESAI! Data mahasiswa berhasil diimport.");

    } catch (\Exception $e) {
        DB::rollBack();
        $this->command->error("FATAL ERROR pada baris $row: " . $e->getMessage());
        // Lanjut ke baris berikutnya (opsional, tapi biasanya kalau format salah mending stop)
        // throw $e; 
    }

    fclose($file);
}

    /**
     * Helper membersihkan angka dari format Excel (Scientific Notation)
     */
    private function cleanNumber($value)
    {
        if (empty($value)) return null;
        $value = trim($value);

        // Handle E+15 (Contoh: 5.31804E+15 -> 5318040000000000)
        if (strpos(strtoupper($value), 'E') !== false) {
            $value = number_format((float)$value, 0, '', '');
        }
        
        // Hapus .0 di belakang
        return str_replace('.0', '', $value);
    }
}