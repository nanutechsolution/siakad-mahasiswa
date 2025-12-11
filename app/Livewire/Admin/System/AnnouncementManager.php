<?php
namespace App\Livewire\Admin\System;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $isModalOpen = false;
    public $isEditMode = false;

    // Form
    public $ann_id, $title, $content, $target_role = 'all', $is_active = true;
    public $attachment, $existing_attachment;

    public function render()
    {
        $announcements = Announcement::where('title', 'like', '%'.$this->search.'%')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('livewire.admin.system.announcement-manager', [
            'announcements' => $announcements
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->reset(['title', 'content', 'target_role', 'attachment', 'existing_attachment']);
        $this->isModalOpen = true;
        $this->isEditMode = false;
    }

    public function edit($id)
    {
        $ann = Announcement::find($id);
        $this->ann_id = $id;
        $this->title = $ann->title;
        $this->content = $ann->content;
        $this->target_role = $ann->target_role;
        $this->is_active = $ann->is_active;
        $this->existing_attachment = $ann->attachment;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'title' => 'required',
            'content' => 'required',
            'attachment' => 'nullable|file|max:2048', // Max 2MB
        ]);

        $path = $this->existing_attachment;
        if ($this->attachment) {
            $path = $this->attachment->store('announcements', 'public');
        }

        Announcement::updateOrCreate(['id' => $this->ann_id], [
            'title' => $this->title,
            'content' => $this->content,
            'target_role' => $this->target_role,
            'is_active' => $this->is_active,
            'attachment' => $path,
            'created_by' => Auth::id()
        ]);

        session()->flash('message', 'Pengumuman berhasil diterbitkan.');
        $this->isModalOpen = false;
    }

    public function delete($id)
    {
        $ann = Announcement::find($id);
        if($ann->attachment) Storage::disk('public')->delete($ann->attachment);
        $ann->delete();
        session()->flash('message', 'Pengumuman dihapus.');
    }
}