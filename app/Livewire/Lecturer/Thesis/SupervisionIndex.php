<?php
namespace App\Livewire\Lecturer\Thesis;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\ThesisSupervisor;

class SupervisionIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $lecturer = Auth::user()->lecturer;
        
        $supervisions = collect();

        if ($lecturer) {
            $supervisions = ThesisSupervisor::with(['thesis.student.user', 'thesis.academic_period'])
                ->where('lecturer_id', $lecturer->id)
                ->whereHas('thesis', function($q) {
                    // Filter pencarian berdasarkan nama mahasiswa atau judul
                    $q->where('title', 'like', '%'.$this->search.'%')
                      ->orWhereHas('student.user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'));
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('livewire.lecturer.thesis.supervision-index', [
            'supervisions' => $supervisions
        ])->layout('layouts.lecturer');
    }
}