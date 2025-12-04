<?php

namespace App\Livewire\Admin\Pmb;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Registrant;
use App\Models\Student;
use App\Models\Billing;
use App\Models\StudyProgram;
use App\Enums\RegistrantStatus;
use App\Mail\PmbStatusUpdate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegistrantIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_status = '';
    public $filter_prodi = '';

    // Modal Detail
    public $isModalOpen = false;
    public $selectedRegistrant;

    public function render()
    {
        $registrants = Registrant::with(['user', 'firstChoice', 'secondChoice'])
            ->when($this->search, function ($q) {
                $q->where('registration_no', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->search . '%'));
            })
            ->when($this->filter_status, fn($q) => $q->where('status', $this->filter_status))
            ->when($this->filter_prodi, fn($q) => $q->where('first_choice_id', $this->filter_prodi))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.pmb.registrant-index', [
            'registrants' => $registrants,
            'prodis' => StudyProgram::all(),
            'statuses' => RegistrantStatus::cases()
        ])->layout('layouts.admin');
    }

    public function showDetail($id)
    {
        $this->selectedRegistrant = Registrant::with('user')->find($id);
        $this->isModalOpen = true;
    }

    // 1. Verifikasi Berkas (Langkah Awal)
    public function verify()
    {
        $this->selectedRegistrant->update(['status' => RegistrantStatus::VERIFIED]);
        session()->flash('message', 'Berkas pendaftar telah diverifikasi. Siap untuk seleksi.');
        $this->isModalOpen = false;
    }

    // 2. Lulus Seleksi (Langkah Kedua)
    public function accept()
    {
        $this->selectedRegistrant->update(['status' => RegistrantStatus::ACCEPTED]);
        // KIRIM EMAIL
        Mail::to($this->selectedRegistrant->user->email)
            ->send(new PmbStatusUpdate($this->selectedRegistrant, 'ACCEPTED'));
        session()->flash('message', 'Selamat! Calon mahasiswa dinyatakan LULUS.');
        $this->isModalOpen = false;
    }

    // 3. Tolak (Jika Gagal)
    public function reject()
    {
        $this->selectedRegistrant->update(['status' => RegistrantStatus::REJECTED]);
        Mail::to($this->selectedRegistrant->user->email)
            ->send(new PmbStatusUpdate($this->selectedRegistrant, 'REJECTED'));
        session()->flash('error', 'Pendaftaran ditolak.');
        $this->isModalOpen = false;
    }

    // 4. MAGIC BUTTON: Daftar Ulang & Generate NIM
    public function promoteToStudent($registrantId)
    {
        DB::transaction(function () use ($registrantId) {
            $camaba = Registrant::with('user', 'firstChoice')->find($registrantId);

            // Cek apakah sudah jadi mahasiswa (biar gak dobel)
            if (Student::where('user_id', $camaba->user_id)->exists()) {
                session()->flash('error', 'User ini sudah terdaftar sebagai mahasiswa.');
                return;
            }

            // A. Generate NIM: Tahun(24) + KodeProdi + Urut(001)
            $year = date('y'); // 25
            $prodiCode = $camaba->firstChoice->code; // TI

            // Hitung urutan
            $count = Student::where('study_program_id', $camaba->first_choice_id)
                ->where('entry_year', date('Y'))
                ->count();

            $noUrut = str_pad($count + 1, 4, '0', STR_PAD_LEFT); // 0001
            $nimBaru = $year . $prodiCode . $noUrut; // 25TI0001

            // B. Insert ke Tabel Students
            $student = Student::create([
                'user_id' => $camaba->user_id,
                'study_program_id' => $camaba->first_choice_id,
                'nim' => $nimBaru,
                'entry_year' => date('Y'),
                'pob' => 'Indonesia', // Default, nanti diedit mhs
                'dob' => now(), // Default
                'gender' => 'L', // Default (Harusnya ada field gender di form PMB)
                'phone' => $camaba->user->email,
                'status' => 'A', // Aktif
            ]);

            // C. Update Akun User (Ubah Role & Username)
            $camaba->user->update([
                'role' => 'student',
                'username' => $nimBaru
            ]);

            // D. Buat Tagihan Uang Pangkal (Opsional)
            Billing::create([
                'student_id' => $student->id,
                'title' => 'Uang Pangkal / Pembangunan',
                'category' => 'GEDUNG',
                'amount' => 5000000, // Rp 5 Juta
                'status' => 'UNPAID',
                'due_date' => now()->addMonth()
            ]);

            // E. Hapus/Arsipkan Data Registrant (Opsional, atau biarkan status ACCEPTED)
            // Kita biarkan status ACCEPTED sebagai histori
        });

        session()->flash('message', 'Berhasil! Akun telah diubah menjadi Mahasiswa Aktif.');
        $this->isModalOpen = false;
    }

    public function export()
    {
        $fileName = 'data_pendaftar_pmb_' . date('Y-m-d_H-i') . '.csv';

        // Ambil data sesuai filter yang sedang aktif
        $data = Registrant::with(['user', 'firstChoice', 'secondChoice'])
            ->when($this->filter_status, fn($q) => $q->where('status', $this->filter_status))
            ->when($this->filter_prodi, fn($q) => $q->where('first_choice_id', $this->filter_prodi))
            ->latest()
            ->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Header Kolom
            fputcsv($file, ['No Pendaftaran', 'Nama Lengkap', 'NIK', 'Asal Sekolah', 'Pilihan 1', 'Pilihan 2', 'Nilai Rapor', 'Status', 'Tanggal Daftar']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->registration_no,
                    $row->user->name,
                    $row->nik,
                    $row->school_name,
                    $row->firstChoice->name ?? '-',
                    $row->secondChoice->name ?? '-',
                    $row->average_grade,
                    $row->status->label(),
                    $row->created_at->format('d-m-Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
