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
use Illuminate\Support\Facades\DB;

class ClassroomManager extends Component
{
    use WithPagination, WithToast;


    public $isImportModalOpen = false;
    public $file_import;

    // Filter & Search
    public $search = '';
    public $active_period_id;

    // Form Master Kelas
    public $class_id, $course_id, $lecturer_id, $name, $quota = 40, $is_open = true;

    // Form Detail Jadwal (Array of Arrays)
    public $schedules_input = [];

    // UI States
    public $isModalOpen = false;
    public $isEditMode = false;

    // Validation Custom Messages (Indonesian)
    protected $messages = [
        'course_id.required' => 'Mata kuliah wajib dipilih.',
        'lecturer_id.required' => 'Dosen pengampu wajib dipilih.',
        'name.required' => 'Nama kelas wajib diisi (misal: A, B, PAGI).',
        'name.max' => 'Nama kelas maksimal 5 karakter.',
        'quota.required' => 'Kuota mahasiswa wajib diisi.',
        'quota.min' => 'Kuota minimal 1 mahasiswa.',
        'schedules_input.*.day.required' => 'Hari wajib dipilih.',
        'schedules_input.*.start_time.required' => 'Jam mulai wajib diisi.',
        'schedules_input.*.end_time.required' => 'Jam selesai wajib diisi.',
        'schedules_input.*.end_time.after' => 'Jam selesai harus lebih akhir dari jam mulai.',
        'schedules_input.*.room_name.required' => 'Ruangan wajib diisi.',
    ];


    // --- FITUR IMPORT CSV ---

    public function openImportModal()
    {
        $this->resetErrorBag();
        $this->file_import = null;
        $this->isImportModalOpen = true;
    }

    public function closeImportModal()
    {
        $this->isImportModalOpen = false;
        $this->file_import = null;
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_jadwal.csv"',
        ];

        // Header CSV
        $columns = ['Kode Matkul', 'Nama Kelas', 'Email/NIDN Dosen (Opsional)', 'Kuota', 'Hari', 'Jam Mulai', 'Jam Selesai', 'Ruangan'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Data Contoh
            fputcsv($file, ['TI101', 'A', 'dosen1@univ.ac.id', '40', 'Senin', '08:00', '10:00', 'R.201']);
            fputcsv($file, ['TI101', 'A', 'dosen1@univ.ac.id', '40', 'Rabu', '08:00', '10:00', 'LAB-1']);
            fputcsv($file, ['SI202', 'PAGI', '', '35', 'Selasa', '13:00', '15:30', 'AULA']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import()
    {
        $this->validate([
            'file_import' => 'required|mimes:csv,txt|max:2048', // Max 2MB
        ]);

        if (!$this->active_period_id) {
            $this->addError('file_import', 'Gagal: Tidak ada Semester Aktif yang diatur.');
            return;
        }

        $path = $this->file_import->getRealPath();
        $handle = fopen($path, 'r');
        fgetcsv($handle); // Skip baris header

        DB::beginTransaction();
        try {
            $countClass = 0;
            $countSchedule = 0;

            while (($row = fgetcsv($handle)) !== FALSE) {
                // Pastikan baris memiliki minimal data yang diperlukan
                if (count($row) < 8) continue;

                [$code, $className, $lecturerIdent, $quota, $day, $start, $end, $room] = $row;

                // 1. Cari Mata Kuliah by Kode
                $course = Course::where('code', trim($code))->first();
                if (!$course) continue; // Skip jika matkul tidak ditemukan

                // 2. Cari Dosen (Opsional) by NIDN/NIP/Email
                $lecturerId = null;
                if (!empty($lecturerIdent)) {
                    $lecturer = Lecturer::where('nidn', trim($lecturerIdent))
                        ->orWhere('nip_internal', trim($lecturerIdent))
                        ->orWhereHas('user', fn($q) => $q->where('email', trim($lecturerIdent)))
                        ->first();
                    $lecturerId = $lecturer ? $lecturer->id : null;
                }

                // 3. Buat/Update Kelas
                // Logic: Kelas dengan Kode Matkul + Nama Kelas + Periode yang sama dianggap satu entitas
                $classroom = Classroom::firstOrCreate(
                    [
                        'academic_period_id' => $this->active_period_id,
                        'course_id' => $course->id,
                        'name' => strtoupper(trim($className)),
                    ],
                    [
                        'lecturer_id' => $lecturerId,
                        'quota' => intval($quota) ?: 40,
                        'is_open' => true,
                    ]
                );

                // Update dosen/kuota jika sudah ada (opsional, agar data terbaru masuk)
                if (!$classroom->wasRecentlyCreated) {
                    $classroom->update([
                        'lecturer_id' => $lecturerId ?? $classroom->lecturer_id,
                        'quota' => intval($quota) ?: $classroom->quota
                    ]);
                } else {
                    $countClass++;
                }

                // 4. Tambahkan Jadwal
                // Cek duplikasi sederhana agar tidak double jika di-upload ulang
                $exists = $classroom->schedules()
                    ->where('day', ucfirst(strtolower(trim($day))))
                    ->where('start_time', trim($start))
                    ->where('room_name', strtoupper(trim($room)))
                    ->exists();

                if (!$exists) {
                    $classroom->schedules()->create([
                        'day' => ucfirst(strtolower(trim($day))),
                        'start_time' => trim($start),
                        'end_time' => trim($end),
                        'room_name' => strtoupper(trim($room)),
                    ]);
                    $countSchedule++;
                }
            }

            DB::commit();
            fclose($handle);

            $this->alertSuccess("Import Selesai! $countClass kelas baru & $countSchedule jadwal ditambahkan.");
            $this->closeImportModal();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('file_import', 'Gagal memproses file: ' . $e->getMessage());
        }
    }



    public function mount()
    {
        $active = AcademicPeriod::where('is_active', true)->first();
        $this->active_period_id = $active ? $active->id : null;

        // Initialize with one empty row if creating new
        if (empty($this->schedules_input)) {
            $this->addScheduleRow();
        }
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
        $this->schedules_input = array_values($this->schedules_input); // Re-index array
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

        if (!$class) {
            $this->alertError('Data kelas tidak ditemukan.');
            return;
        }

        $this->class_id = $id;
        $this->course_id = $class->course_id;
        $this->lecturer_id = $class->lecturer_id;
        $this->name = $class->name;
        $this->quota = $class->quota;
        $this->is_open = (bool) $class->is_open;

        $this->schedules_input = [];
        foreach ($class->schedules as $sch) {
            $this->schedules_input[] = [
                'day' => $sch->day,
                'start_time' => Carbon::parse($sch->start_time)->format('H:i'),
                'end_time' => Carbon::parse($sch->end_time)->format('H:i'),
                'room_name' => $sch->room_name,
            ];
        }

        if (empty($this->schedules_input)) {
            $this->addScheduleRow();
        }

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

        if (!$this->active_period_id) {
            $this->addError('general', 'Tidak ada Semester Aktif yang sedang berjalan. Silakan atur di Pengaturan.');
            return;
        }

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

        // Simpan Jadwal (Delete old, Insert new strategy for simplicity)
        $classroom->schedules()->delete();

        foreach ($this->schedules_input as $sch) {
            $classroom->schedules()->create([
                'day' => $sch['day'],
                'start_time' => $sch['start_time'],
                'end_time' => $sch['end_time'],
                'room_name' => strtoupper($sch['room_name']),
            ]);
        }

        session()->flash('message', 'Data Kelas & Jadwal berhasil disimpan!');
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function checkScheduleConflicts()
    {
        // 1. Cek Bentrok Internal (Input vs Input di form yang sama)
        foreach ($this->schedules_input as $i => $s1) {
            foreach ($this->schedules_input as $j => $s2) {
                if ($i !== $j && $s1['day'] == $s2['day']) {
                    if ($this->isTimeOverlap($s1['start_time'], $s1['end_time'], $s2['start_time'], $s2['end_time'])) {
                        $this->addError("schedules_input.{$i}.start_time", "Waktu ini bentrok dengan jadwal baris ke-" . ($j + 1) . " di form ini.");
                        return true;
                    }
                }
            }
        }

        // Siapkan Variabel untuk Query
        $activePeriodId = $this->active_period_id;
        $currentClassId = $this->class_id;
        $currentLecturerId = $this->lecturer_id;

        // 2. Cek Bentrok Database
        foreach ($this->schedules_input as $index => $input) {

            // A. Cek Bentrok DOSEN (Jika dosen dipilih)
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
                    $matkul = $lecturerConflict->classroom->course->name ?? 'Unknown';
                    $kelas = $lecturerConflict->classroom->name ?? '?';
                    $jam = Carbon::parse($lecturerConflict->start_time)->format('H:i') . '-' . Carbon::parse($lecturerConflict->end_time)->format('H:i');

                    $msg = "Dosen bentrok! Sedang mengajar '$matkul' (Kelas $kelas) pada jam $jam.";
                    $this->addError("schedules_input.{$index}.start_time", $msg);
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
                    $matkul = $roomConflict->classroom->course->name ?? 'Unknown';
                    $kelas = $roomConflict->classroom->name ?? '?';
                    $jam = Carbon::parse($roomConflict->start_time)->format('H:i') . '-' . Carbon::parse($roomConflict->end_time)->format('H:i');

                    $msg = "Ruangan '{$input['room_name']}' dipakai '$matkul' (Kls $kelas) jam $jam.";
                    $this->addError("schedules_input.{$index}.room_name", $msg);
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
        $class = Classroom::find($id);
        if ($class) {
            $class->schedules()->delete(); // Hapus jadwal dulu
            $class->delete();
            session()->flash('message', 'Kelas berhasil dihapus.');
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->class_id = null;
        $this->course_id = '';
        $this->lecturer_id = '';
        $this->name = '';
        $this->quota = 40;
        $this->is_open = true;
        $this->schedules_input = [];
        $this->addScheduleRow(); // Reset ke 1 baris kosong
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.academic.classroom-manager', [
            'classrooms' => Classroom::with(['course', 'lecturer.user', 'schedules'])
                ->where('academic_period_id', $this->active_period_id)
                ->when($this->search, function ($q) {
                    $q->whereHas('course', fn($c) => $c->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%'));
                })
                ->latest()
                ->paginate(10),
            'courses' => Course::where('is_active', true)->orderBy('name')->get(),
            'lecturers' => Lecturer::where('is_active', true)->with('user')->get()->sortBy('user.name'), // Sort by user name manually or via query if joined
        ])->layout('layouts.admin');
    }
}
