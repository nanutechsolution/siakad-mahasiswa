<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\StudyProgram;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RealLecturerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lokasi File CSV
        // Pastikan Anda sudah menaruh file "dosen_real.csv" di folder "database/csv/"
        $csvFile = database_path('csv/dosen_real.csv');

        if (!file_exists($csvFile)) {
            $this->command->error("File tidak ditemukan di: $csvFile");
            $this->command->info("Silakan export Excel ke CSV dan simpan di folder database/csv/");
            return;
        }

        $this->command->info('Mulai import data dosen dari CSV...');

        // 2. Buka File
        $file = fopen($csvFile, 'r');
        $row = 0;
        $password = Hash::make('password'); // Password default
        
        // Cache Prodi ID biar ngebut (tidak query berulang)
        $prodis = StudyProgram::all()->pluck('id', 'name'); // [ 'Teknik Informatika' => 1, ... ]

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                $row++;

                // Skip Header (7 Baris pertama di file Anda adalah Kop Surat/Header)
                // Baris ke-8 adalah judul kolom (No, NIDN, Nama...)
                // Data mulai baris ke-9
                if ($row < 9) continue;

                // Mapping Kolom CSV (Sesuaikan indeks array dengan file CSV Anda)
                // 0: No
                // 1: NIDN (802046404.0)
                // 2: NUPTK (734742643130122.0)
                // 3: Nama (ALEXANDER ADIS)
                // 4: Program Studi (D3 Manajemen Informatika)
                // 5: L/P
                // 6: Tempat Tgl Lahir
                
                // A. Bersihkan Data NIDN & NIP (Hapus .0 di belakang jika ada)
                // FIX: Pastikan NIP unik atau null jika duplikat/kosong
                $nidn = $this->cleanNumber($data[1] ?? null);
                $nipRaw = $this->cleanNumber($data[2] ?? null);

                // Jika NIP kosong, set null agar tidak kena unique constraint (asumsi kolom nullable)
                // Jika NIP panjang (seperti NUPTK), potong atau biarkan string
                $nip = empty($nipRaw) ? null : (string) $nipRaw;

                $fullName = trim($data[3] ?? '');
                $prodiRaw = trim($data[4] ?? ''); // "D3 Manajemen Informatika"

                // Validasi: Skip jika Nama kosong
                // NIDN/NIP boleh kosong salah satu, tapi jangan dua-duanya
                if (empty($fullName) || (empty($nidn) && empty($nip))) {
                    continue;
                }

                // B. Pisahkan Gelar (Sederhana)
                // Contoh: "STEFANUS DWI, M.KOM" -> Nama: STEFANUS DWI, Gelar Blkg: M.KOM
                $nameParts = explode(',', $fullName);
                $realName = trim($nameParts[0]);
                $backTitle = isset($nameParts[1]) ? trim($nameParts[1]) : null;
                if (isset($nameParts[2])) $backTitle .= ', ' . trim($nameParts[2]);

                // C. Cari ID Prodi
                // Logika: CSV "S1 Teknik Informatika" -> Kita cari "Teknik Informatika" di DB
                $prodiId = null;
                // Coba cari yang mengandung nama prodi di DB
                foreach ($prodis as $dbProdiName => $dbProdiId) {
                    // Case insensitive search
                    if (stripos($prodiRaw, $dbProdiName) !== false) {
                        $prodiId = $dbProdiId;
                        break;
                    }
                }

                // D. Buat User Login
                // Username pakai NIDN (jika ada) atau NIP
                // Pastikan username unik
                $username = $nidn ?: $nip;
                
                // Jika username masih kosong (misal data aneh), skip
                if (empty($username)) {
                    $this->command->warn("Skip baris $row: Username kosong.");
                    continue;
                }

                $email = $username . '@dosen.unmaris.ac.id'; // Email dummy dari NIDN

                // Cek apakah user sudah ada (avoid duplicate entry error for users table)
                $user = User::where('username', $username)->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $fullName, // Nama lengkap di User tetap pakai gelar biar sopan
                        'username' => $username,
                        'email' => $email,
                        'password' => $password,
                        'role' => 'lecturer',
                        'email_verified_at' => now(),
                    ]);
                }

                // E. Buat Data Dosen
                // FIX: Gunakan updateOrCreate dengan kunci unik yang tepat untuk menghindari duplikasi
                // Kita cari berdasarkan user_id karena itu pasti unik 1-on-1
                
                // Cek apakah NIP ini sudah dipakai orang lain? Jika ya, set null untuk menghindari error
                if ($nip) {
                    $existingNip = Lecturer::where('nip_internal', $nip)->where('user_id', '!=', $user->id)->exists();
                    if ($existingNip) {
                        $this->command->warn("Duplikat NIP ditemukan: $nip untuk $fullName. Set NIP ke NULL.");
                        $nip = null; 
                    }
                }
                 // Cek apakah NIDN ini sudah dipakai orang lain? Jika ya, set null
                if ($nidn) {
                    $existingNidn = Lecturer::where('nidn', $nidn)->where('user_id', '!=', $user->id)->exists();
                    if ($existingNidn) {
                        $this->command->warn("Duplikat NIDN ditemukan: $nidn untuk $fullName. Set NIDN ke NULL.");
                         $nidn = null;
                    }
                }

                Lecturer::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'study_program_id' => $prodiId, // Bisa null jika prodi tidak match
                        'nidn' => $nidn,
                        'nip_internal' => $nip,
                        'front_title' => null, // CSV ini tidak memisahkan gelar depan, biarkan null atau edit manual nanti
                        'back_title' => $backTitle,
                        'phone' => null, // Data CSV tidak ada HP
                        'is_active' => true,
                    ]
                );

                $this->command->info("Imported: $fullName ($username)");
            }

            DB::commit();
            $this->command->info("SELESAI! Semua data dosen berhasil diimport.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error pada baris $row: " . $e->getMessage());
        }

        fclose($file);
    }

    /**
     * Helper untuk membersihkan format angka Excel (misal "12345.0" jadi "12345")
     */
    private function cleanNumber($value)
    {
        if (empty($value)) return null;
        
        // Hapus spasi
        $value = trim($value);

        // Handle Scientific Notation (E+15)
        if (strpos(strtoupper($value), 'E') !== false) {
            $value = number_format((float)$value, 0, '', '');
        }

        // Hapus .0 di belakang
        if (str_ends_with($value, '.0')) {
            $value = substr($value, 0, -2);
        }

        return $value;
    }
}