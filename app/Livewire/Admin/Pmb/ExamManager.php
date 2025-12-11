<?php

namespace App\Livewire\Admin\Pmb;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ExamQuestion;

class ExamManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;
    public $isEditMode = false;

    // Form Fields
    public $question_id;
    public $question_text;
    public $option_a, $option_b, $option_c, $option_d;
    public $correct_answer = 'A';

    public function render()
    {
        $questions = ExamQuestion::where('question_text', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.pmb.exam-manager', [
            'questions' => $questions
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $q = ExamQuestion::findOrFail($id);
        
        $this->question_id = $id;
        $this->question_text = $q->question_text;
        
        $this->option_a = $q->option_a;
        $this->option_b = $q->option_b;
        $this->option_c = $q->option_c;
        $this->option_d = $q->option_d;
        
        $this->correct_answer = $q->correct_answer;
        
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    // UPDATE: Tambahkan parameter $closeModal
    public function store($closeModal = true)
    {
        $this->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        ExamQuestion::updateOrCreate(['id' => $this->question_id], [
            'question_text' => $this->question_text,
            'option_a' => $this->option_a,
            'option_b' => $this->option_b,
            'option_c' => $this->option_c,
            'option_d' => $this->option_d,
            'correct_answer' => $this->correct_answer,
            'points' => 5
        ]);

        session()->flash('message', $this->isEditMode ? 'Soal diperbarui.' : 'Soal ditambahkan.');

        // Jika Edit Mode, paksa tutup (karena aneh kalau edit tapi reset)
        if ($this->isEditMode || $closeModal) {
            $this->isModalOpen = false;
            $this->resetForm();
        } else {
            // Jika Mode Tambah Lagi: Reset form tapi biarkan modal terbuka
            $this->resetForm();
            // Optional: Kirim event ke JS untuk scroll ke atas modal atau fokus ke input pertama
            $this->dispatch('question-saved'); 
        }
    }

    public function delete($id)
    {
        ExamQuestion::find($id)->delete();
        session()->flash('message', 'Soal dihapus.');
    }

    private function resetForm()
    {
        $this->reset(['question_id', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer']);
        $this->correct_answer = 'A';
    }
}