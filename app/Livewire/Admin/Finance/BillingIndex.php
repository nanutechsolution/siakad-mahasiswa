<?php

namespace App\Livewire\Admin\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Billing;
use App\Models\Student;
use App\Models\AcademicPeriod;
use App\Models\StudyProgram;
use App\Models\TuitionRate;
use App\Models\FeeType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillingIndex extends Component
{
    use WithPagination;

    // Filter & Search
    public $search = '';
    public $filter_status = '';
    public $filter_fee_type = '';
    
    // State
    public $active_period;
    public $isModalOpen = false;
    public $isDetailModalOpen = false;
    public $selectedBillingDetail;

    // Detail Pembayaran (Modal)
    public $total_paid = 0;
    public $remaining_balance = 0;

    // Form Create
    public $target_type = 'prodi'; 
    public $prodi_id;
    public $entry_year;
    public $specific_student_nim;
    
    // Default Status Filter: Aktif
    public $target_student_status = 'A'; 
    
    // Form Detail Tagihan
    public $title;
    public $fee_type_id; 
    public $semester;
    public $use_manual_amount = false;
    public $amount;
    public $due_date;
    public $skip_duplicates = true;

    // Summary Real-time
    public $summary = [
        'total_bill' => 0,
        'count_paid' => 0,
        'count_unpaid' => 0,
        'count_partial' => 0,
    ];

    public function mount()
    {
        $this->active_period = AcademicPeriod::where('is_active', true)->first();
        $this->entry_year = date('Y');
        $this->due_date = date('Y-m-d', strtotime('+1 month'));
    }

    public function create() {
        $this->reset(['prodi_id', 'entry_year', 'specific_student_nim', 'title', 'amount', 'fee_type_id', 'semester', 'use_manual_amount']);
        $this->target_type = 'prodi';
        $this->target_student_status = 'A'; 
        $this->entry_year = date('Y');
        $this->due_date = date('Y-m-d', strtotime('+1 month'));
        $this->skip_duplicates = true;
        
        $spp = FeeType::where('code', 'SPP')->first();
        if($spp) {
            $this->fee_type_id = $spp->id;
            $this->updatedFeeTypeId($spp->id);
        }
        $this->isModalOpen = true;
    }

    public function updatedFeeTypeId($value) {
        $type = FeeType::find($value);
        if ($type) {
            $periodName = $this->active_period->name ?? '';
            $this->title = $type->name . ($periodName ? ' - ' . $periodName : '');
        }
    }

    public function store() {
        // VALIDASI LENGKAP & PINTAR
        $this->validate([
            'fee_type_id' => 'required|exists:fee_types,id',
            'title'       => 'required|string|max:255',
            'due_date'    => 'required|date|after_or_equal:today', // Tidak boleh tanggal lampau
            'semester'    => 'nullable|integer|min:1|max:14',
            
            // Validasi Amount: Jika manual, wajib isi dan minimal 1 rupiah
            'amount'      => $this->use_manual_amount ? 'required|numeric|min:1' : 'nullable',
            
            'target_type' => 'required|in:prodi,angkatan,individual',
            
            // Validasi Target Dinamis
            'prodi_id'    => 'required_if:target_type,prodi',
            'entry_year'  => [
                'required_if:target_type,angkatan', // Wajib jika target angkatan
                'nullable', 
                'digits:4',      // Harus 4 digit (misal: 2024)
                'integer',       // Harus angka
                'min:2000',      // Validasi logis tahun minimal
                'max:'.(date('Y') + 1) // Validasi logis tahun maksimal
            ],
            'specific_student_nim' => 'required_if:target_type,individual',
            'target_student_status' => 'required|in:A,C,N,D,L,K,All', 
        ], [
            // Custom Error Messages (Bahasa Indonesia yang Ramah UX)
            'fee_type_id.required' => 'Wajib memilih Jenis Biaya (misal: SPP/Gedung).',
            'fee_type_id.exists'   => 'Jenis Biaya yang dipilih tidak valid.',
            
            'title.required' => 'Judul Tagihan tidak boleh kosong.',
            'title.max'      => 'Judul Tagihan terlalu panjang (maksimal 255 karakter).',
            
            'due_date.required'       => 'Mohon tentukan Tanggal Jatuh Tempo.',
            'due_date.after_or_equal' => 'Tanggal Jatuh Tempo tidak boleh hari kemarin/lampau.',
            
            'semester.integer' => 'Semester harus berupa angka bulat.',
            'semester.min'     => 'Semester minimal 1.',
            'semester.max'     => 'Semester maksimal 14.',
            
            'amount.required' => 'Nominal wajib diisi karena Anda mengaktifkan Mode Manual.',
            'amount.min'      => 'Nominal tagihan manual minimal Rp 1.',
            
            'prodi_id.required_if' => 'Program Studi wajib dipilih untuk target Per Prodi.',
            
            'entry_year.required_if' => 'Tahun Angkatan wajib diisi untuk target Per Angkatan.',
            'entry_year.digits'      => 'Format Tahun Angkatan salah. Masukkan 4 digit (contoh: 2024).',
            'entry_year.min'         => 'Tahun Angkatan tidak valid (terlalu lampau).',
            'entry_year.max'         => 'Tahun Angkatan melebihi tahun depan.',
            
            'specific_student_nim.required_if' => 'NIM / No. Registrasi wajib diisi untuk target Individu.',
            
            'target_student_status.required' => 'Status Akademik target harus dipilih.',
        ]);

        $studentsQuery = Student::with('study_program');

        // Jika targetnya INDIVIDU, abaikan filter status (cari di semua status)
        if ($this->target_type !== 'individual' && $this->target_student_status !== 'All') {
            $studentsQuery->where('status', $this->target_student_status);
        }

        if ($this->target_type == 'prodi') {
            $studentsQuery->where('study_program_id', $this->prodi_id);
            if (!empty($this->entry_year)) $studentsQuery->where('entry_year', $this->entry_year);
        } elseif ($this->target_type == 'angkatan') {
            $studentsQuery->where('entry_year', $this->entry_year);
        } elseif ($this->target_type == 'individual') {
            $studentsQuery->where('nim', $this->specific_student_nim);
        }
        
        $students = $studentsQuery->get();

        if ($students->isEmpty()) {
            // Gunakan addError agar muncul di field target (lebih terlihat user daripada flash message)
            if ($this->target_type == 'individual') {
                $this->addError('specific_student_nim', 'Mahasiswa dengan NIM tersebut tidak ditemukan.');
            } else {
                $this->addError('target_type', 'Tidak ada mahasiswa yang ditemukan dengan kriteria Program Studi/Angkatan/Status tersebut.');
            }
            return;
        }

        // Prefetch Rates
        $ratesMap = [];
        if (!$this->use_manual_amount) {
            $rates = TuitionRate::where('fee_type_id', $this->fee_type_id)
                ->whereIn('study_program_id', $students->pluck('study_program_id')->unique())
                ->whereIn('entry_year', $students->pluck('entry_year')->unique())
                ->get();
            foreach ($rates as $r) $ratesMap[$r->study_program_id . '-' . $r->entry_year] = $r; 
        }

        DB::beginTransaction();
        try {
            $count = 0;
            $skipped_zero = 0;
            $skipped_duplicate = 0;

            foreach ($students as $student) {
                if ($this->skip_duplicates) {
                    $exists = Billing::where('student_id', $student->id)
                        ->where('fee_type_id', $this->fee_type_id)
                        ->where('academic_period_id', $this->active_period->id ?? null)
                        ->when($this->semester, fn($q) => $q->where('semester', $this->semester))
                        ->exists();
                    if ($exists) {
                        $skipped_duplicate++;
                        continue;
                    }
                }

                $nominal = 0;
                $rateId = null;
                if ($this->use_manual_amount) {
                    $nominal = $this->amount;
                } else {
                    $key = $student->study_program_id . '-' . $student->entry_year;
                    $rateSource = $ratesMap[$key] ?? null;
                    if ($rateSource) {
                        $nominal = $rateSource->amount;
                        $rateId = $rateSource->id;
                    }
                }

                if ($nominal <= 0) {
                    $skipped_zero++;
                    continue;
                }

                Billing::create([
                    'student_id' => $student->id,
                    'academic_period_id' => $this->active_period->id ?? null,
                    'fee_type_id' => $this->fee_type_id,
                    'tuition_rate_id' => $rateId,
                    'semester' => $this->semester,
                    'title' => $this->title,
                    'description' => 'Tagihan generate otomatis',
                    'amount' => $nominal,
                    'due_date' => $this->due_date,
                    'status' => 'UNPAID'
                ]);
                $count++;
            }
            DB::commit();
            
            // --- LOGIC PINTAR VALIDASI MODAL (POST-PROCESS) ---
            if ($count === 0) {
                // Analisa penyebab kegagalan dan beri pesan spesifik
                if ($skipped_zero > 0) {
                    // Case: Gagal karena Tarif Master Kosong
                    $this->addError('amount', "Gagal memproses! Ditemukan $skipped_zero mahasiswa, tetapi Tarif Master untuk Prodi/Angkatan mereka belum disetting (Nominal 0). Silakan setting tarif dulu atau gunakan Mode Manual.");
                } elseif ($skipped_duplicate > 0) {
                    // Case: Gagal karena Duplikat Semua
                    // Flash error global karena ini bukan salah input, tapi kondisi data
                    session()->flash('error', "Proses dibatalkan. Semua mahasiswa yang dipilih ($skipped_duplicate orang) sudah memiliki tagihan ini sebelumnya.");
                } else {
                    session()->flash('error', "Tidak ada tagihan yang berhasil dibuat. Periksa kembali data mahasiswa.");
                }
                
                // PENTING: Jangan tutup modal agar user bisa baca error dan perbaiki
                return; 
            }

            // Jika Sukses, baru tutup modal
            $msg = "Sukses! $count tagihan dibuat.";
            if($skipped_duplicate > 0) $msg .= " ($skipped_duplicate duplikat dilewati).";
            if($skipped_zero > 0) $msg .= " ($skipped_zero dilewati krn nominal 0).";

            session()->flash('message', $msg);
            $this->isModalOpen = false;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error System: ' . $e->getMessage());
            // Tetap buka modal jika error sistem agar user tau
        }
    }

    public function showDetail($id)
    {
        $this->selectedBillingDetail = Billing::with(['student.user', 'registrant.user', 'payments', 'fee_type'])->findOrFail($id);
        
        $this->total_paid = $this->selectedBillingDetail->payments
            ->where('status', 'VERIFIED')
            ->sum('amount_paid');

        $this->remaining_balance = max(0, $this->selectedBillingDetail->amount - $this->total_paid);

        $this->isDetailModalOpen = true;
    }

    public function render()
    {
        $query = Billing::with(['student.user', 'registrant.user', 'fee_type']) 
            ->where(function($q) {
                $q->whereHas('student.user', fn($sq) => $sq->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhereHas('student', fn($sq) => $sq->where('nim', 'like', '%'.$this->search.'%'))
                  ->orWhereHas('registrant.user', fn($rq) => $rq->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhereHas('registrant', fn($rq) => $rq->where('registration_no', 'like', '%'.$this->search.'%'));
            })
            ->when($this->filter_status, fn($q) => $q->where('status', $this->filter_status))
            ->when($this->filter_fee_type, fn($q) => $q->where('fee_type_id', $this->filter_fee_type));

        $summaryQuery = clone $query;
        $this->summary['total_bill'] = $summaryQuery->sum('amount');
        
        $statusCounts = $summaryQuery->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $this->summary['count_paid'] = $statusCounts['PAID'] ?? 0;
        $this->summary['count_unpaid'] = $statusCounts['UNPAID'] ?? 0;
        $this->summary['count_partial'] = $statusCounts['PARTIAL'] ?? 0;

        $billings = $query->latest()->paginate(10);

        return view('livewire.admin.finance.billing-index', [
            'billings' => $billings,
            'prodis' => StudyProgram::all(),
            'fee_types' => FeeType::where('is_active', true)->get()
        ])->layout('layouts.admin');
    }
}