<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AcademicPeriod;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\Setting; 
use App\Models\LetterRequest; 
use App\Enums\KrsStatus;

class PrintController extends Controller
{
    public function printKrs()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $student->load('studyProgram.faculty');
        $active_period = AcademicPeriod::where('is_active', true)->first();

        if (!$active_period) {
            return redirect()->back()->with('error', 'Tidak ada periode akademik aktif.');
        }

        // Refaktor: Ambil Header dan Detailnya
        $krs = StudyPlan::with(['details.courseClass.course', 'details.courseClass.classSchedules', 'details.courseClass.lecturers.user'])
            ->where('student_id', $student->id)
            ->where('academic_period_id', $active_period->id)
            ->whereIn('status', [KrsStatus::SUBMITTED->value, KrsStatus::APPROVED->value])
            ->first();

        if (!$krs) {
            return redirect()->back()->with('error', 'KRS belum diisi atau belum diajukan.');
        }

        $total_sks = $krs->details->sum(fn($item) => $item->courseClass->course->credit_total ?? 0);
        $setting = Setting::first();

        $pdf = Pdf::loadView('pdf.krs', [
            'student' => $student,
            'period' => $active_period,
            'krs' => $krs,
            'data' => $krs->details,
            'total_sks' => $total_sks,
            'setting' => $setting,
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

        $student->load('studyProgram.faculty');

        $period = $request->has('period_id') 
            ? AcademicPeriod::find($request->period_id) 
            : AcademicPeriod::where('is_active', true)->first();

        if (!$period) {
            return redirect()->back()->with('error', 'Periode akademik tidak ditemukan.');
        }

        // Refaktor: Ambil Detail melalui Header
        $khs = StudyPlan::with(['details.courseClass.course'])
            ->where('student_id', $student->id)
            ->where('academic_period_id', $period->id)
            ->where('status', KrsStatus::APPROVED->value)
            ->first();

        if (!$khs) {
            return redirect()->back()->with('error', 'Data KHS tidak ditemukan atau belum disetujui.');
        }

        $total_sks = $khs->details->sum(fn($item) => $item->courseClass->course->credit_total ?? 0);
        $total_bobot = $khs->details->sum(fn($item) => ($item->courseClass->course->credit_total ?? 0) * ($item->grade_point ?? 0));
        $ips = $total_sks > 0 ? number_format($total_bobot / $total_sks, 2) : 0;

        $setting = Setting::first();

        $pdf = Pdf::loadView('pdf.khs', [
            'student' => $student,
            'period' => $period,
            'data' => $khs->details,
            'total_sks' => $total_sks,
            'total_bobot' => $total_bobot,
            'ips' => $ips,
            'setting' => $setting,
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

        $student->load('studyProgram.faculty');
        $setting = Setting::first();

        // Refaktor: Ambil langsung dari Detail yang status headernya Approved
        $raw_grades = StudyPlanDetail::whereHas('studyPlan', function($q) use ($student) {
                $q->where('student_id', $student->id)
                  ->where('status', KrsStatus::APPROVED->value);
            })
            ->with(['courseClass.course', 'studyPlan.academicPeriod'])
            ->whereNotNull('grade_point')
            ->get();

        // FILTER: Ambil Nilai Terbaik Saja (GroupBy Course ID)
        $clean_grades = $raw_grades->groupBy('courseClass.course_id')
            ->map(function ($attempts) {
                return $attempts->sortByDesc('grade_point')->first();
            })
            ->sortBy('courseClass.course.semester_default');

        $total_sks = $clean_grades->sum(fn($i) => $i->courseClass->course->credit_total ?? 0);
        $total_bobot = $clean_grades->sum(fn($i) => ($i->courseClass->course->credit_total ?? 0) * ($i->grade_point ?? 0));
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

        // Status 'active' (lowercase) sesuai refaktor status mahasiswa
        if ($student->status !== 'active') {
            return redirect()->back()->with('error', 'Anda tidak berstatus Aktif. Tidak dapat mencetak surat.');
        }

        $student->load('studyProgram.faculty');
        $setting = Setting::first();
        $active_period = AcademicPeriod::where('is_active', true)->first();
        
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

    public function printExamCard()
    {
        $user = Auth::user();
        $student = $user->student;
        $active_period = AcademicPeriod::where('is_active', true)->first();

        if (!$student || !$active_period) {
            return redirect()->back()->with('error', 'Data tidak valid.');
        }

        // Cek Tagihan
        $unpaid_bills = $student->billings()
            ->where('academic_period_id', $active_period->id)
            ->where('status', 'unpaid')
            ->exists();

        if ($unpaid_bills) {
            return redirect()->back()->with('error', 'Kartu Ujian terkunci. Harap lunasi tagihan terlebih dahulu.');
        }

        // Ambil matkul via Header-Detail
        $krs = StudyPlan::with(['details.courseClass.course', 'details.courseClass.classSchedules'])
            ->where('student_id', $student->id)
            ->where('academic_period_id', $active_period->id)
            ->where('status', KrsStatus::APPROVED->value)
            ->first();

        if (!$krs || $krs->details->isEmpty()) {
            return redirect()->back()->with('error', 'KRS belum disetujui.');
        }

        $student->load('studyProgram.faculty');
        $setting = Setting::first();

        $pdf = Pdf::loadView('pdf.exam-card', [
            'student' => $student,
            'period' => $active_period,
            'data' => $krs->details,
            'setting' => $setting,
            'printed_at' => now()->format('d F Y H:i')
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Kartu_Ujian_' . $student->nim . '.pdf');
    }

    public function printLetter($id)
    {
        $user = Auth::user();
        $request = LetterRequest::with(['student.user', 'student.studyProgram.faculty'])
            ->where('id', $id)
            ->first();
            
        if (!$request) {
            return redirect()->back()->with('error', 'Surat tidak ditemukan.');
        }

        if ($request->status !== 'completed') {
            return redirect()->back()->with('error', 'Surat belum selesai diproses.');
        }

        $student = $request->student;
        $setting = Setting::first();
        $active_period = AcademicPeriod::where('is_active', true)->first();

        $pdf = Pdf::loadView('pdf.general-letter', [
            'request' => $request,
            'student' => $student,
            'user' => $student->user,
            'setting' => $setting,
            'period' => $active_period,
            'date' => now()->format('d F Y')
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Surat_' . str_replace(' ', '_', $request->type) . '_' . $student->nim . '.pdf');
    }
}