<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Registrant;
use App\Models\Billing;
use App\Models\StudyProgram;
use App\Models\TuitionRate;
use App\Models\FeeType;
use App\Enums\RegistrantStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PmbSyncController extends Controller
{
    public function store(Request $request)
    {
        // 1. Security check
        if ($request->input('secret_key') !== env('PMB_API_SECRET')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $input = $request->all();

        if (!empty($input['prodi_code'])) {
            $prodi = StudyProgram::where('name', $input['prodi_code'])->first();

            if (!$prodi) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Program studi tidak ditemukan di SIAKAD',
                    'detail'  => [
                        'prodi_code' => $input['prodi_code']
                    ]
                ], 422);
            }

            // 🔁 Ganti nama menjadi kode
            $input['prodi_code'] = $prodi->code;
        }


        // 2. Mapping key lokal ke english
        if (!isset($input['mother_name']) && isset($input['nama_ibu'])) $input['mother_name'] = $input['nama_ibu'];
        if (!isset($input['school_name']) && isset($input['asal_sekolah'])) $input['school_name'] = $input['asal_sekolah'];
        if (!isset($input['entry_year']) && isset($input['tahun_masuk'])) $input['entry_year'] = $input['tahun_masuk'];

        // 3. Validasi
        $validator = Validator::make($input, [
            'registration_no' => 'required',
            'nik' => 'required|numeric',
            'name' => 'required|string',
            'email' => 'required|email',
            'prodi_code' => 'required|exists:study_programs,code',
            'entry_year' => 'required|digits:4',
            'mother_name' => 'required|string',
        ], [
            'prodi_code.exists' => 'Program studi tidak terdaftar di SIAKAD'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $prodi = StudyProgram::where('code', $input['prodi_code'])->firstOrFail();

            // Hitung nominal SPP
            // --- LOGIC TARIF SPP (BEST PRACTICE) ---
            $sppType = FeeType::where('code', 'SPP')->first();
            $nominalSpp = 0;

            if ($sppType) {
                $rate = TuitionRate::where('study_program_id', $prodi->id)
                    ->where('entry_year', $input['entry_year'])
                    ->where('fee_type_id', $sppType->id)
                    ->first();

                if ($rate) {
                    $nominalSpp = $rate->amount;
                }
            }
            // Jika belum ada tarif, kirim response error
            if ($nominalSpp <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Tarif SPP untuk Prodi {$prodi->name} tahun {$input['entry_year']} belum di-set."
                ], 422);
            }
            // 4. Buat User Camaba
            $defaultPassword = 'pmb' . $input['entry_year'];

            $user = User::updateOrCreate(
                ['email' => $input['email']],
                [
                    'name' => $input['name'],
                    'username' => 'camaba_' . rand(1000, 9999),
                    'password' => Hash::make($defaultPassword),
                    'role' => 'camaba',
                    'email_verified_at' => now(),
                ]
            );

            // 5. Buat Registrant
            $registrant = Registrant::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'registration_no' => $input['registration_no'],
                    'period_year' => $input['entry_year'],
                    'track' => $input['jalur_masuk'] ?? 'JALUR_PMB_ONLINE',
                    'first_choice_id' => $prodi->id,
                    'nik' => $input['nik'],
                    'school_name' => $input['school_name'] ?? '-',
                    'mother_name' => $input['mother_name'],
                    'parent_phone' => $input['nomor_hp_ortu'] ?? null,
                    'status' => RegistrantStatus::ACCEPTED,
                ]
            );

            // 6. Buat Tagihan SPP (Billing)
            if ($sppType) {
                Billing::firstOrCreate(
                    [
                        'registrant_id' => $registrant->id,
                        'fee_type_id' => $sppType->id,
                    ],
                    [
                        'title' => 'Biaya Daftar Ulang & SPP Semester 1',
                        'category' => 'SPP',
                        'description' => 'Tagihan otomatis import PMB. Silakan lunasi/cicil untuk mendapatkan NIM.',
                        'amount' => $nominalSpp,
                        'status' => 'UNPAID',
                        'due_date' => now()->addDays(30),
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Camaba & tagihan SPP berhasil dibuat.',
                'credentials' => [
                    'email' => $user->email,
                    'password' => $defaultPassword,
                    'login_url' => url('/login')
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
