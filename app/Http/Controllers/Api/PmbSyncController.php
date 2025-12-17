<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Registrant;
use App\Models\StudyProgram; // Asumsi di SIAKAD ada model ini
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PmbSyncController extends Controller
{
    /**
     * Handle incoming data from PMB Application
     */
    public function store(Request $request)
    {

        // ====================================================
        // 🔒 SECURITY LAYER (VIA ENV)
        // ====================================================
        
        // Ambil kunci rahasia dari file .env SIAKAD
        $validSecret = env('PMB_API_SECRET'); 

        // Validasi jika ENV belum diset di server
        if (!$validSecret) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server Error: Konfigurasi PMB_API_SECRET belum diset di .env SIAKAD.'
            ], 500);
        }

        // Cek apakah kunci yang dikirim cocok
        if ($request->input('secret_key') !== $validSecret) {
            return response()->json([
                'status' => 'error',
                'message' => 'UNAUTHORIZED: Kunci rahasia tidak cocok.'
            ], 401);
        }
        // ====================================================
        // 1. Validasi Input dari PMB
        // Kita longgarkan sedikit validasinya karena asumsinya data dari PMB sudah valid
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email', // Cek unique dilakukan manual nanti
            'nomor_hp' => 'required|string', // Tetap divalidasi meski tidak masuk tabel users (bisa masuk JSON documents jika perlu)
            'nik' => 'required|string',
            'nisn' => 'nullable|string',
            'asal_sekolah' => 'required|string',
            'tahun_lulus' => 'required|integer',
            'nama_ayah' => 'nullable|string',
            'nama_ibu' => 'nullable|string',
            'pilihan_prodi_1' => 'required|string', // Menerima NAMA prodi, bukan ID
            'pilihan_prodi_2' => 'nullable|string',
            'jalur_masuk' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 2. Cek atau Buat User di SIAKAD
            // Cek apakah email sudah ada di SIAKAD
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                // Generate Password Default (Misal: Tanggal Lahir atau NIK)
                $defaultPassword = $request->nik;

                // Membuat User baru sesuai struktur tabel 'users' SIAKAD
                // Pastikan Model User di SIAKAD menggunakan Trait HasUlids untuk generate ID char(26)
                $user = User::create([
                    'name' => $request->name,
                    'username' => $request->nik, // Menggunakan NIK sebagai username (Wajib Unique)
                    'email' => $request->email,
                    'password' => Hash::make($defaultPassword),
                    'role' => 'student', // Default sesuai schema
                    'is_active' => 1,    // Default sesuai schema
                ]);
            }

            // 3. Mapping Nama Prodi ke ID Prodi (SIAKAD)
            // PMB kirim string "Teknik Informatika", SIAKAD cari ID-nya di tabel study_programs
            $prodi1 = StudyProgram::where('name', 'LIKE', '%' . $request->pilihan_prodi_1 . '%')->first();
            $prodi2 = $request->pilihan_prodi_2
                ? StudyProgram::where('name', 'LIKE', '%' . $request->pilihan_prodi_2 . '%')->first()
                : null;

            if (!$prodi1) {
                throw new \Exception("Program Studi 1 tidak ditemukan di Database SIAKAD: " . $request->pilihan_prodi_1);
            }

            // 4. Generate Nomor Pendaftaran Unik (Logic sederhana)
            $regNo = 'REG-' . date('Y') . '-' . strtoupper(Str::random(5));

            // 5. Simpan ke Tabel REGISTRANTS
            $registrant = Registrant::create([
                'user_id' => $user->id, // Foreign Key (char 26)
                'registration_no' => $regNo,
                'period_year' => date('Y'),
                'track' => strtoupper($request->jalur_masuk), // REGULER, PRESTASI, dll
                'first_choice_id' => $prodi1->id,
                'second_choice_id' => $prodi2 ? $prodi2->id : null,
                'nik' => $request->nik,
                'nisn' => $request->nisn,
                'school_name' => $request->asal_sekolah,
                'school_major' => 'IPA/IPS', // Default atau minta dari PMB
                'average_grade' => 0, // Default 0
                'father_name' => $request->nama_ayah,
                'mother_name' => $request->nama_ibu,
                'parent_phone' => null, // Bisa diambil dari request jika ada input khusus ortu
                'documents' => [], // Kosongkan atau kirim JSON link file
                'status' => 'ACCEPTED', // Status awal masuk SIAKAD
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disinkronisasi ke SIAKAD',
                'data' => [
                    'nim_sementara' => $regNo,
                    'user_id' => $user->id,
                    'registrant_id' => $registrant->id
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
