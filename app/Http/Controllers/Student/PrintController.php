<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AcademicPeriod;
use App\Models\StudyPlan;
use App\Models\Setting; // <--- Import Model Setting

class PrintController extends Controller
{
    public function printKrs()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // Load relasi Fakultas untuk kop surat
        $student->load('study_program.faculty');

        $active_period = AcademicPeriod::where('is_active', true)->first();

        if (!$active_period) {
            return redirect()->back()->with('error', 'Tidak ada periode akademik aktif.');
        }

        $krs_data = StudyPlan::with(['classroom.course', 'classroom.schedules', 'classroom.lecturer.user'])
            ->where('student_id', $student->id)
            ->where('academic_period_id', $active_period->id)
            ->whereIn('status', ['SUBMITTED', 'APPROVED'])
            ->get();

        $total_sks = $krs_data->sum(function ($item) {
            return $item->classroom->course->credit_total;
        });

        // AMBIL SETTING KAMPUS
        $setting = Setting::first();

        $pdf = Pdf::loadView('pdf.krs', [
            'student' => $student,
            'period' => $active_period,
            'data' => $krs_data,
            'total_sks' => $total_sks,
            'setting' => $setting, // <--- Kirim ke View
            'printed_at' => now()->format('d F Y H:i')
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('KRS_' . $student->nim . '_' . $active_period->code . '.pdf');
    }

    public function printKhs(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // Load relasi Fakultas
        $student->load('study_program.faculty');

        if ($request->has('period_id')) {
            $period = AcademicPeriod::find($request->period_id);
        } else {
            $period = AcademicPeriod::where('is_active', true)->first();
        }

        if (!$period) {
            return redirect()->back()->with('error', 'Periode akademik tidak ditemukan.');
        }

        $khs_data = StudyPlan::with(['classroom.course'])
            ->where('student_id', $student->id)
            ->where('academic_period_id', $period->id)
            ->where('status', 'APPROVED')
            ->get();

        $total_sks = $khs_data->sum(fn($item) => $item->classroom->course->credit_total);

        $total_bobot = $khs_data->sum(function ($item) {
            return $item->classroom->course->credit_total * $item->grade_point;
        });

        $ips = $total_sks > 0 ? number_format($total_bobot / $total_sks, 2) : 0;

        // AMBIL SETTING KAMPUS
        $setting = Setting::first();

        $pdf = Pdf::loadView('pdf.khs', [
            'student' => $student,
            'period' => $period,
            'data' => $khs_data,
            'total_sks' => $total_sks,
            'total_bobot' => $total_bobot,
            'ips' => $ips,
            'setting' => $setting, // <--- Kirim ke View
            'printed_at' => now()->format('d F Y')
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('KHS_' . $student->nim . '_' . $period->code . '.pdf');
    }


    public function printTranscript()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');

        $student->load('study_program.faculty');
        $setting = Setting::first();

        // Ambil Semua Nilai Approved
        // $grades = StudyPlan::with(['classroom.course', 'academic_period'])
        //     ->where('student_id', $student->id)
        //     ->where('status', 'APPROVED')
        //     ->get()
        //     ->sortBy(function ($q) {
        //         // Urutkan berdasarkan Semester (1, 2, 3...)
        //         return $q->classroom->course->semester_default;
        //     });

        $raw_grades = StudyPlan::with(['classroom.course', 'academic_period'])
            ->where('student_id', $student->id)
            ->where('status', 'APPROVED')
            ->whereNotNull('grade_point')
            ->get();

        // FILTER: Ambil Nilai Terbaik Saja
        $clean_grades = $raw_grades->groupBy('classroom.course_id')
            ->map(function ($attempts) {
                return $attempts->sortByDesc('grade_point')->first();
            })
            ->sortBy(function ($q) {
                // Urutkan berdasarkan Semester Default Matkul (1, 2, 3...)
                return $q->classroom->course->semester_default;
            });

        $total_sks = $clean_grades->sum(fn($i) => $i->classroom->course->credit_total);
        $total_bobot = $clean_grades->sum(fn($i) => $i->classroom->course->credit_total * $i->grade_point);
        $ipk = $total_sks > 0 ? number_format($total_bobot / $total_sks, 2) : 0.00;

        $pdf = Pdf::loadView('pdf.transcript', [
            'student' => $student,
            'data' => $clean_grades,
            'total_sks' => $total_sks,
            'total_bobot' => $total_bobot,
            'ipk' => $ipk,
            'setting' => $setting,
            'printed_at' => now()->format('d F Y')
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Transkrip_' . $student->nim . '.pdf');
    }

      public function printActiveStudent()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) return redirect()->back();

        // 1. Validasi: Harus Status Aktif
        if ($student->status !== 'A') {
            return redirect()->back()->with('error', 'Anda tidak berstatus Aktif. Tidak dapat mencetak surat.');
        }

        // 2. Data Pendukung
        $student->load('study_program.faculty');
        $setting = Setting::first();
        $active_period = AcademicPeriod::where('is_active', true)->first();
        
        // Nomor Surat Otomatis (Format: NO/AKTIF/TAHUN/NIM) - Bisa disesuaikan
        $nomor_surat = "109/UNMARIS/BAAK/" . date('Y') . "/" . $student->nim;

        $pdf = Pdf::loadView('pdf.active-letter', [
            'student' => $student,
            'user' => $user,
            'setting' => $setting,
            'period' => $active_period,
            'nomor_surat' => $nomor_surat,
            'date' => now()->format('d F Y')
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Surat_Aktif_' . $student->nim . '.pdf');
    }
}
