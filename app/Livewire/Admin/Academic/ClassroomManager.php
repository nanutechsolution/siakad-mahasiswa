<?php

namespace App\Livewire\Admin\Academic;

use App\Models\Classroom;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\AcademicPeriod;
use App\Models\Schedule;
use App\Traits\WithToast;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class ClassroomManager extends Component
{
    use WithPagination, WithToast;

    // Filter
    public $search = '';
    public $active_period_id;

    // Form Master Kelas
    public $class_id, $course_id, $lecturer_id, $name, $quota = 40, $is_open = true;

    // Form Detail Jadwal (Array)
    public $schedules_input = [];

    public $isModalOpen = false;
    public $isEditMode = false;

    public function mount()
    {
        $active = AcademicPeriod::where('is_active', true)->first();
        $this->active_period_id = $active ? $active->id : null;

        $this->addScheduleRow();
    }

    public function addScheduleRow()
    {
        $this->schedules_input[] = [
            'day' => 'Senin',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'room_name' => ''
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
        $class = Classroom::with('schedules')->find($id);

        $this->class_id = $id;
        $this->course_id = $class->course_id;
        $this->lecturer_id = $class->lecturer_id;
        $this->name = $class->name;
        $this->quota = $class->quota;
        $this->is_open = $class->is_open;

        $this->schedules_input = [];
        foreach ($class->schedules as $sch) {
            $this->schedules_input[] = [
                'day' => $sch->day,
                'start_time' => Carbon::parse($sch->start_time)->format('H:i'),
                'end_time' => Carbon::parse($sch->end_time)->format('H:i'),
                'room_name' => $sch->room_name,
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
            'name' => 'required|max:5',
            'quota' => 'required|integer|min:1',
            'schedules_input.*.day' => 'required',
            'schedules_input.*.start_time' => 'required',
            'schedules_input.*.end_time' => 'required|after:schedules_input.*.start_time',
            'schedules_input.*.room_name' => 'required',
        ]);

        // Validasi Bentrok (Cek Dosen & Ruangan)
        if ($this->checkScheduleConflicts()) {
            return; // Stop jika bentrok
        }

        // Simpan Kelas
        $classroom = Classroom::updateOrCreate(
            ['id' => $this->class_id],
            [
                'academic_period_id' => $this->active_period_id,
                'course_id' => $this->course_id,
                'lecturer_id' => $this->lecturer_id ?: null,
                'name' => strtoupper($this->name),
                'quota' => $this->quota,
                'is_open' => $this->is_open,
            ]
        );

        // Simpan Jadwal
        $classroom->schedules()->delete();

        foreach ($this->schedules_input as $sch) {
            $classroom->schedules()->create([
                'day' => $sch['day'],
                'start_time' => $sch['start_time'],
                'end_time' => $sch['end_time'],
                'room_name' => $sch['room_name'],
            ]);
        }

        $this->alertSuccess('Kelas & Jadwal berhasil disimpan!');
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function checkScheduleConflicts()
    {
        // 1. Cek Bentrok Internal (Input vs Input)
        foreach ($this->schedules_input as $i => $s1) {
            foreach ($this->schedules_input as $j => $s2) {
                if ($i !== $j && $s1['day'] == $s2['day']) {
                    if ($this->isTimeOverlap($s1['start_time'], $s1['end_time'], $s2['start_time'], $s2['end_time'])) {
                        $this->addError("schedules_input.{$i}.start_time", "Bentrok dengan baris jadwal ke-" . ($j + 1));
                        session()->flash('error', "Jadwal baris ke-" . ($i + 1) . " bentrok dengan baris ke-" . ($j + 1));
                        return true;
                    }
                }
            }
        }

        // Siapkan Variabel untuk Query (Penting agar terbaca di dalam function)
        $activePeriodId = $this->active_period_id;
        $currentClassId = $this->class_id;
        $currentLecturerId = $this->lecturer_id;

        // 2. Cek Bentrok Database
        foreach ($this->schedules_input as $index => $input) {

            // A. Cek Bentrok DOSEN
            if ($currentLecturerId) {
                $lecturerConflict = Schedule::whereHas('classroom', function ($q) use ($activePeriodId, $currentClassId, $currentLecturerId) {
                    $q->where('academic_period_id', $activePeriodId)
                        ->where('lecturer_id', $currentLecturerId);

                    // PENTING: Jangan cek bentrok dengan diri sendiri saat Edit
                    if ($currentClassId) {
                        $q->where('id', '!=', $currentClassId);
                    }
                })
                    ->where('day', $input['day'])
                    ->where(function ($q) use ($input) {
                        // Logika Overlap Waktu
                        $q->where('start_time', '<', $input['end_time'])
                            ->where('end_time', '>', $input['start_time']);
                    })
                    ->with('classroom.course')
                    ->first();

                if ($lecturerConflict) {
                    $matkul = $lecturerConflict->classroom->course->name;
                    $kelas = $lecturerConflict->classroom->name;
                    $jam = Carbon::parse($lecturerConflict->start_time)->format('H:i') . '-' . Carbon::parse($lecturerConflict->end_time)->format('H:i');

                    $msg = "Dosen bentrok! Sedang mengajar $matkul - Kls $kelas ($jam)";
                    $this->addError("schedules_input.{$index}.start_time", "Jadwal Dosen Bentrok");
                    session()->flash('error', $msg);
                    return true;
                }
            }

            // B. Cek Bentrok RUANGAN
            if (!empty($input['room_name'])) {
                $roomConflict = Schedule::whereHas('classroom', function ($q) use ($activePeriodId, $currentClassId) {
                    $q->where('academic_period_id', $activePeriodId);
                    if ($currentClassId) {
                        $q->where('id', '!=', $currentClassId);
                    }
                })
                    ->where('day', $input['day'])
                    ->where('room_name', $input['room_name'])
                    ->where(function ($q) use ($input) {
                        $q->where('start_time', '<', $input['end_time'])
                            ->where('end_time', '>', $input['start_time']);
                    })
                    ->with('classroom.course')
                    ->first();

                if ($roomConflict) {
                    $matkul = $roomConflict->classroom->course->name;
                    $kelas = $roomConflict->classroom->name;
                    $jam = Carbon::parse($roomConflict->start_time)->format('H:i') . '-' . Carbon::parse($roomConflict->end_time)->format('H:i');

                    $msg = "Ruangan '{$input['room_name']}' terpakai oleh $matkul - Kls $kelas ($jam)";
                    $this->addError("schedules_input.{$index}.room_name", "Ruangan Terpakai");
                    session()->flash('error', $msg);
                    return true;
                }
            }
        }

        return false;
    }

    private function isTimeOverlap($start1, $end1, $start2, $end2)
    {
        return ($start1 < $end2) && ($end1 > $start2);
    }

    public function delete($id)
    {
        Classroom::find($id)->delete();
        session()->flash('message', 'Kelas dihapus.');
    }

    private function resetForm()
    {
        $this->class_id = null;
        $this->course_id = '';
        $this->lecturer_id = '';
        $this->name = '';
        $this->quota = 40;
        $this->schedules_input = [];
        $this->addScheduleRow();
    }

    public function render()
    {
        return view('livewire.admin.academic.classroom-manager', [
            'classrooms' => Classroom::with(['course', 'lecturer', 'schedules'])
                ->where('academic_period_id', $this->active_period_id)
                ->when($this->search, function ($q) {
                    $q->whereHas('course', fn($c) => $c->where('name', 'like', '%' . $this->search . '%'));
                })
                ->paginate(10),
            'courses' => Course::where('is_active', true)->orderBy('name')->get(),
            'lecturers' => Lecturer::where('is_active', true)->with('user')->get(),
        ])->layout('layouts.admin');
    }
}
