<?php

namespace App\Livewire\Admin\Lpm;

use Livewire\Component;
use App\Models\EdomQuestion;

class EdomIndex extends Component
{
    // HAPUS $questions dari sini karena bikin error serialization
    // public $questions; <--- HAPUS INI

    public $isModalOpen = false;
    public $isEditMode = false;

    // Form
    public $question_id, $category = 'Pedagogik', $question_text, $is_active = true;

    public function render()
    {
        // Ambil data langsung di sini dan kirim ke view
        // Ini mencegah error "getMorphClass" karena data tidak disimpan di state Livewire
        $questions = EdomQuestion::orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return view('livewire.admin.lpm.edom-index', [
            'questions' => $questions
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->reset(['question_id', 'category', 'question_text', 'is_active']);
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $q = EdomQuestion::find($id);
        $this->question_id = $id;
        $this->category = $q->category;
        $this->question_text = $q->question_text;
        $this->is_active = (bool) $q->is_active;
        
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'category' => 'required',
            'question_text' => 'required',
        ]);

        EdomQuestion::updateOrCreate(['id' => $this->question_id], [
            'category' => $this->category,
            'question_text' => $this->question_text,
            'is_active' => $this->is_active,
            'sort_order' => $this->isEditMode ? EdomQuestion::find($this->question_id)->sort_order : (EdomQuestion::max('sort_order') + 1)
        ]);

        session()->flash('message', 'Pertanyaan berhasil disimpan.');
        $this->isModalOpen = false;
    }
    
    public function delete($id)
    {
        EdomQuestion::find($id)->delete();
        session()->flash('message', 'Pertanyaan dihapus.');
    }
}