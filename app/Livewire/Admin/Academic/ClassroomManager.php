<?php

namespace App\Livewire\Admin\Academic;

use App\Models\CourseClass;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\AcademicPeriod;
use App\Models\ClassSchedule;
use App\Traits\WithToast;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClassroomManager extends Component
{
    use WithPagination, WithToast;

    public $isImportModalOpen = false;
    public $file_import;

    // Filter & Search
    public $search = '';
    public $active_period_id;

    // Form Master Kelas
    public $class_id, $course_id, $lecturer_id, $name, $quota = 40, $is_active = true;

    // Form Detail Jadwal
    public $schedules_input = [];

    // UI States
    public $isModalOpen = false;
    public $isEditMode = false;

    protected $messages = [
        'course_id.required' => 'Mata kuliah wajib dipilih.',
        'lecturer_id.required' => 'Dosen pengampu wajib dipilih.',
        'name.required' => 'Nama kelas wajib diisi.',
        'name.max' => 'Nama kelas maksimal 50 karakter.',
        'quota.required' => 'Kuota mahasiswa wajib diisi.',
        'schedules_input.*.day_of_week.required' => 'Hari wajib dipilih.',
        'schedules_input.*.start_time.required' => 'Jam mulai wajib diisi.',
        'schedules_input.*.end_time.required' => 'Jam selesai wajib diisi.',
        'schedules_input.*.end_time.after' => 'Jam selesai harus lebih akhir.',
        'schedules_input.*.room_name.required' => 'Ruangan wajib diisi.',
    ];

    public function mount()
    {
        $active = AcademicPeriod::where('is_active', true)->first();
        $this->active_period_id = $active ? $active->id : null;

        if (empty($this->schedules_input)) {
            $this->addScheduleRow();
        }
    }

    public function addScheduleRow()
    {
        $this->schedules_input[] = [
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '10:00',
            'room_name' => '',
            'type' => 'theory'
        ];
    }

    public function removeScheduleRow($index)
    {
        unset($this->schedules_input[$index]);
        $this->schedules_input = array_values($this->schedules_input);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $class = CourseClass::with(['classSchedules', 'lecturers'])->findOrFail($id);

        $this->class_id = $id;
        $this->course_id = $class->course_id;
        $this->name = $class->name;
        $this->quota = $class->quota;
        $this->is_active = (bool) $class->is_active;

        // Ambil Dosen Utama (Primary)
        $this->lecturer_id = $class->lecturers()->wherePivot('is_primary', true)->first()?->id;

        $this->schedules_input = [];
        foreach ($class->classSchedules as $sch) {
            $this->schedules_input[] = [
                'day_of_week' => $sch->day_of_week,
                'start_time' => Carbon::parse($sch->start_time)->format('H:i'),
                'end_time' => Carbon::parse($sch->end_time)->format('H:i'),
                'room_name' => $sch->room_name,
                'type' => $sch->type,
            ];
        }

        if (empty($this->schedules_input)) $this->addScheduleRow();

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'course_id' => 'required',
            'lecturer_id' => 'required',
            'name' => 'required|max:50',
            'quota' => 'required|integer|min:1',
            'schedules_input.*.day_of_week' => 'required',
            'schedules_input.*.start_time' => 'required',
            'schedules_input.*.end_time' => 'required|after:schedules_input.*.start_time',
            'schedules_input.*.room_name' => 'required',
        ]);

        if (!$this->active_period_id) {
            $this->addError('general', 'Periode aktif tidak ditemukan.');
            return;
        }

        if ($this->checkScheduleConflicts()) return;

        DB::transaction(function () {
            $classroom = CourseClass::updateOrCreate(
                ['id' => $this->class_id],
                [
                    'id' => $this->class_id ?? (string) Str::ulid(),
                    'academic_period_id' => $this->active_period_id,
                    'course_id' => $this->course_id,
                    'name' => strtoupper($this->name),
                    'quota' => $this->quota,
                    'is_active' => $this->is_active,
                ]
            );

            // Sync Dosen Pengampu (Set as Primary)
            $classroom->lecturers()->sync([
                $this->lecturer_id => [
                    'id' => (string) Str::ulid(),
                    'is_primary' => true,
                    'can_input_grade' => true
                ]
            ]);

            // Re-sync Jadwal
            $classroom->classSchedules()->delete();
            foreach ($this->schedules_input as $sch) {
                $classroom->classSchedules()->create([
                    'id' => (string) Str::ulid(),
                    'day_of_week' => $sch['day_of_week'],
                    'start_time' => $sch['start_time'],
                    'end_time' => $sch['end_time'],
                    'room_name' => strtoupper($sch['room_name']),
                    'type' => $sch['type'] ?? 'theory',
                ]);
            }
        });

        session()->flash('message', 'Data Kelas & Jadwal berhasil disimpan!');
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function checkScheduleConflicts()
    {
        foreach ($this->schedules_input as $index => $input) {
            $roomConflict = ClassSchedule::whereHas('courseClass', function ($q) {
                    $q->where('academic_period_id', $this->active_period_id);
                    if ($this->class_id) $q->where('id', '!=', $this->class_id);
                })
                ->where('day_of_week', $input['day_of_week'])
                ->where('room_name', $input['room_name'])
                ->where(function ($q) use ($input) {
                    $q->where('start_time', '<', $input['end_time'])
                      ->where('end_time', '>', $input['start_time']);
                })
                ->first();

            if ($roomConflict) {
                $this->addError("schedules_input.{$index}.room_name", "Ruangan ini sudah terpakai di jam tersebut.");
                return true;
            }
        }
        return false;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        $class = CourseClass::find($id);
        if ($class) {
            $class->delete();
            session()->flash('message', 'Kelas berhasil dihapus.');
        }
    }

    private function resetForm()
    {
        $this->reset(['class_id', 'course_id', 'lecturer_id', 'name', 'quota', 'is_active', 'schedules_input']);
        $this->addScheduleRow();
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.academic.classroom-manager', [
            'classrooms' => CourseClass::with(['course', 'classSchedules', 'lecturers.user'])
                ->where('academic_period_id', $this->active_period_id)
                ->when($this->search, function ($q) {
                    $q->whereHas('course', fn($c) => $c->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%'));
                })
                ->latest()
                ->paginate(10),
            'courses' => Course::where('is_active', true)->orderBy('name')->get(),
            'lecturers' => Lecturer::with('user')->where('is_active',1)->get()->sortBy('user.name'),
        ])->layout('layouts.admin');
    }
}