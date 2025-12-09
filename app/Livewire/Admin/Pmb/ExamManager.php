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

    // Form Fields (Pastikan ini ada)
    public $question_id;
    public $question_text;
    public $options = [
        'A' => '',
        'B' => '',
        'C' => '',
        'D' => '',
    ];
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

        // Update cara ambil data ke array
        $this->options = [
            'A' => $q->option_a,
            'B' => $q->option_b,
            'C' => $q->option_c,
            'D' => $q->option_d,
        ];

        $this->correct_answer = $q->correct_answer;
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {

        $this->validate([
            'question_text' => 'required|string',
            'options.A'     => 'required|string', // Validasi array
            'options.B'     => 'required|string',
            'options.C'     => 'required|string',
            'options.D'     => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        ExamQuestion::updateOrCreate(['id' => $this->question_id], [
            'question_text' => $this->question_text,
            // Ambil dari array options
            'option_a' => $this->options['A'],
            'option_b' => $this->options['B'],
            'option_c' => $this->options['C'],
            'option_d' => $this->options['D'],
            'correct_answer' => $this->correct_answer,
            'points' => 5
        ]);

        session()->flash('message', $this->isEditMode ? 'Soal diperbarui.' : 'Soal baru ditambahkan.');
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['question_id', 'question_text', 'correct_answer']);
        // Reset array manual
        $this->options = ['a' => '', 'b' => '', 'c' => '', 'd' => ''];
        $this->correct_answer = 'A';
    }
    public function delete($id)
    {
        ExamQuestion::find($id)->delete();
        session()->flash('message', 'Soal dihapus.');
    }
}
