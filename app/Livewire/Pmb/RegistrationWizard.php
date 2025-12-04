<?php

namespace App\Livewire\Pmb;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\StudyProgram;
use App\Models\Registrant;
use App\Enums\RegistrantStatus;
use App\Models\PmbWave;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class RegistrationWizard extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    public $totalSteps = 4;

    // Data Binding
    // Step 1: Data Diri
    public $nik, $nisn, $phone;

    // Step 2: Sekolah & Ortu
    public $school_name, $school_major, $average_grade;
    public $father_name, $mother_name, $parent_phone;

    // Step 3: Pilihan Prodi
    public $first_choice_id, $second_choice_id;

    // Step 4: Upload Berkas
    public $file_ijazah, $file_ktp;

    public $active_wave;
    public $is_registration_open = false;

    public function mount()
    {
        $this->active_wave = PmbWave::where('is_active', true)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->first();

        if ($this->active_wave) {
            $this->is_registration_open = true;
        }
        // Cek jika user sudah punya data, load ke form (Draft)
        $registrant = Registrant::where('user_id', Auth::id())->first();
        if ($registrant) {
            if ($registrant->status !== RegistrantStatus::DRAFT) {
                // Jika sudah submit, redirect ke halaman status
                return redirect()->route('pmb.status');
            }

            // Load data draft
            $this->nik = $registrant->nik;
            $this->school_name = $registrant->school_name;
            // ... load sisanya ...
        }
    }

    public function nextStep()
    {
        $this->validateStep($this->currentStep);
        $this->currentStep++;
    }

    public function prevStep()
    {
        $this->currentStep--;
    }

    public function validateStep($step)
    {
        if ($step == 1) {
            $this->validate([
                'nik' => 'required|numeric|digits:16',
                'nisn' => 'required|numeric',
                'phone' => 'required|numeric',
            ]);
        } elseif ($step == 2) {
            $this->validate([
                'school_name' => 'required|string',
                'average_grade' => 'required|numeric|min:0|max:100',
                'father_name' => 'required|string',
            ]);
        } elseif ($step == 3) {
            $this->validate([
                'first_choice_id' => 'required|exists:study_programs,id',
                'second_choice_id' => 'nullable|exists:study_programs,id|different:first_choice_id',
            ]);
        }
    }

    public function submit()
    {
        // Validasi Akhir (Termasuk File)
        $this->validate([
            'file_ijazah' => 'required|mimes:pdf,jpg,jpeg|max:2048',
            'file_ktp' => 'required|mimes:pdf,jpg,jpeg|max:2048',
        ]);

        // Upload File
        $pathIjazah = $this->file_ijazah->store('pmb/ijazah', 'public');
        $pathKtp = $this->file_ktp->store('pmb/ktp', 'public');

        // Generate No Pendaftaran
        $regNo = 'PMB-' . date('Y') . '-' . str_pad(Auth::id(), 4, '0', STR_PAD_LEFT);

        // Simpan ke Database
        Registrant::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'registration_no' => $regNo,
                'period_year' => date('Y'),
                'track' => 'REGULER',
                'nik' => $this->nik,
                'nisn' => $this->nisn,
                'school_name' => $this->school_name,
                'school_major' => $this->school_major,
                'average_grade' => $this->average_grade,
                'father_name' => $this->father_name,
                'mother_name' => $this->mother_name,
                'parent_phone' => $this->parent_phone,
                'first_choice_id' => $this->first_choice_id,
                'second_choice_id' => $this->second_choice_id,
                'documents' => [
                    'ijazah' => $pathIjazah,
                    'ktp' => $pathKtp
                ],
                'status' => RegistrantStatus::SUBMITTED // Kunci Data
            ]
        );

        return redirect()->route('pmb.status')->with('success', 'Pendaftaran Berhasil Dikirim!');
    }

    public function render()
    {
        return view('livewire.pmb.registration-wizard', [
            'prodis' => StudyProgram::all()
        ])->layout('layouts.pmb');
    }
}
