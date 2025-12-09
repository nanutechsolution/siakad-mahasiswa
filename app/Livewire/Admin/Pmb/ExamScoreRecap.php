<?php

namespace App\Livewire\Admin\Pmb;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ExamAttempt;
use App\Models\Registrant;
use App\Models\StudyProgram;
use App\Enums\RegistrantStatus;
use Illuminate\Support\Facades\DB;

class ExamScoreRecap extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_prodi = '';
    public $min_score = 0; // Filter nilai minimal

    public function render()
    {
        // Ambil Data Percobaan Ujian (ExamAttempt) yang sudah selesai
        $results = ExamAttempt::with(['registrant.user', 'registrant.firstChoice'])
            ->where('status', 'FINISHED')
            ->when($this->search, function($q) {
                $q->whereHas('registrant.user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhereHas('registrant', fn($r) => $r->where('registration_no', 'like', '%'.$this->search.'%'));
            })
            ->when($this->filter_prodi, function($q) {
                $q->whereHas('registrant', fn($r) => $r->where('first_choice_id', $this->filter_prodi));
            })
            ->when($this->min_score > 0, function($q) {
                $q->where('total_score', '>=', (int)$this->min_score);
            })
            ->orderByDesc('total_score') // Ranking dari tertinggi
            ->paginate(20);

        return view('livewire.admin.pmb.exam-score-recap', [
            'results' => $results,
            'prodis' => StudyProgram::all()
        ])->layout('layouts.admin');
    }

    // Aksi Cepat: Luluskan Peserta
    public function passRegistrant($registrantId)
    {
        $registrant = Registrant::find($registrantId);
        
        if ($registrant && $registrant->status !== RegistrantStatus::ACCEPTED) {
            $registrant->update(['status' => RegistrantStatus::ACCEPTED]);
            session()->flash('message', "Peserta {$registrant->registration_no} dinyatakan LULUS.");
        }
    }

    // Aksi Cepat: Gagal
    public function failRegistrant($registrantId)
    {
        $registrant = Registrant::find($registrantId);
        
        if ($registrant) {
            $registrant->update(['status' => RegistrantStatus::REJECTED]);
            session()->flash('error', "Peserta {$registrant->registration_no} dinyatakan TIDAK LULUS.");
        }
    }

    public function export()
    {
        $fileName = 'rekap_nilai_pmb_' . date('Y-m-d_H-i') . '.csv';

        // Query ulang untuk export (tanpa pagination)
        $data = ExamAttempt::with(['registrant.user', 'registrant.firstChoice'])
            ->where('status', 'FINISHED')
            ->when($this->filter_prodi, fn($q) => $q->whereHas('registrant', fn($r) => $r->where('first_choice_id', $this->filter_prodi)))
            ->orderByDesc('total_score')
            ->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Rank', 'No Pendaftaran', 'Nama Peserta', 'Prodi Pilihan 1', 'Skor Ujian', 'Waktu Mulai', 'Waktu Selesai', 'Status Kelulusan']);

            foreach ($data as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row->registrant->registration_no,
                    $row->registrant->user->name,
                    $row->registrant->firstChoice->name ?? '-',
                    $row->total_score,
                    $row->started_at->format('d/m/Y H:i'),
                    $row->finished_at->format('d/m/Y H:i'),
                    $row->registrant->status->label()
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}