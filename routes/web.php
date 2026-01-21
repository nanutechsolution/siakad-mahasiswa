<?php

use App\Http\Controllers\Student\PrintController;
use App\Livewire\Admin\Finance\BillingIndex;
use App\Livewire\Admin\Finance\PaymentVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

// Route::get('/pmb', \App\Livewire\Pmb\Landing::class)->name('home');
// Route::get('/info-pmb', \App\Livewire\Pmb\Info::class)->name('pmb.info');
// Rute untuk USER yang sudah LOGIN
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. Dashboard GENERAL (Polisi Lalu Lintas)
    // Kalau user akses /dashboard, kita cek role dia, lalu lempar ke tempat yang benar
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'student') {
            return redirect()->route('student.dashboard');
        } elseif ($user->role === 'lecturer') {
            return redirect()->route('lecturer.dashboard');
        } elseif ($user->role === 'camaba') {
            return redirect()->route('pmb.register');
        }
        return abort(403); // Role tidak dikenali
    })->name('dashboard');


    // 2. Dashboard ADMIN
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
        Route::get('/settings', \App\Livewire\Admin\Settings::class)->name('settings');

        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/billings', \App\Livewire\Admin\Finance\BillingIndex::class)->name('billings');
            // Route::get('/finance/billings', BillingIndex::class)->name('finance.billings');
            Route::get('/verify', PaymentVerification::class)->name('verify');
            Route::get('/tuition-rates', \App\Livewire\Admin\Finance\TuitionRateManager::class)->name('rates');
            Route::get('/fee-type', \App\Livewire\Admin\Finance\FeeTypeManager::class)->name('fee.type');
        });

        Route::prefix('master')->name('master.')->group(function () {
            Route::get('/faculties', \App\Livewire\Admin\Master\FacultyIndex::class)->name('faculties');
            Route::get('/prodi', \App\Livewire\Admin\Master\ProdiIndex::class)->name('prodi');
            Route::get('/lecturers', \App\Livewire\Admin\Master\LecturerIndex::class)->name('lecturers');
            Route::get('/students', \App\Livewire\Admin\Master\StudentIndex::class)->name('students');
            Route::get('/periods', \App\Livewire\Admin\Master\AcademicPeriodIndex::class)->name('periods');
        });

        Route::prefix('academic')->name('academic.')->group(function () {
            Route::get('/courses', \App\Livewire\Admin\Academic\CourseIndex::class)->name('courses');
            Route::get('/classrooms', \App\Livewire\Admin\Academic\ClassroomManager::class)->name('classrooms');
            Route::get('/krs-management', \App\Livewire\Admin\Academic\KrsManagement::class)->name('krs-management');
            Route::get('/krs-validation', \App\Livewire\Admin\Academic\KrsValidate::class)->name('krs-validation');
            Route::get('/krs-generate', \App\Livewire\Admin\Academic\KrsGenerate::class)->name('krs-generate');
            Route::get('/theses', \App\Livewire\Admin\Academic\ThesisManager::class)->name('theses');
            Route::get('/advisor-plotting', \App\Livewire\Admin\Academic\AdvisorPlotting::class)->name('advisor.plotting');
            Route::get('/import-schedule', \App\Livewire\Admin\Academic\ImportSchedule::class)->name('import.schedule');
            Route::get('/import-course', \App\Livewire\Admin\Academic\ImportCourse::class)->name('import.course');
        });

        Route::prefix('lpm')->name('lpm.')->group(function () {
            Route::get('/edom-master', \App\Livewire\Admin\Lpm\EdomIndex::class)->name('edom.master');
            Route::get('/edom-result', \App\Livewire\Admin\Lpm\EdomResult::class)->name('edom.result');
        });
        Route::prefix('pmb')->name('pmb.')->group(function () {
            // Route::get('/dashboard', \App\Livewire\Admin\Pmb\PmbDashboard::class)->name('dashboard');
            Route::get('/registrants', \App\Livewire\Admin\Pmb\RegistrantIndex::class)->name('registrants');
            // Route::get('/waves', \App\Livewire\Admin\Pmb\WaveManagement::class)->name('waves');
            // Route::get('/exams', \App\Livewire\Admin\Pmb\ExamManager::class)->name('exams');
            // Route::get('/exam-recap', \App\Livewire\Admin\Pmb\ExamScoreRecap::class)->name('exam.recap');
        });

        Route::get('/settings/nim', \App\Livewire\Admin\Settings\NimConfig::class)->name('settings.nim');
        Route::get('/users', \App\Livewire\Admin\System\UserManagement::class)->name('users');
        Route::get('/announcements', \App\Livewire\Admin\System\AnnouncementManager::class)->name('announcements');
        Route::get('/letters', \App\Livewire\Admin\System\LetterManager::class)->name('admin.letters');
    });

    Route::prefix('lecturer')->name('lecturer.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Lecturer\Dashboard::class)->name('dashboard');
        Route::get('/grading', \App\Livewire\Lecturer\GradingIndex::class)->name('grading.index');
        Route::get('/grading/{classId}', \App\Livewire\Lecturer\Grading::class)->name('grading');
        Route::get('/krs-validation', \App\Livewire\Lecturer\KrsValidation::class)->name('krs.validation');
        Route::get('/edom-report', \App\Livewire\Lecturer\EdomReport::class)->name('edom.report');
        Route::get('/attendance/{classId}', \App\Livewire\Lecturer\Attendance\MeetingManager::class)->name('attendance');
        Route::get('/print/attendance/{classId}', [\App\Http\Controllers\Lecturer\PrintController::class, 'printAttendanceRecap'])->name('print.attendance');

        Route::prefix('thesis')->name('thesis.')->group(function () {
            Route::get('/', \App\Livewire\Lecturer\Thesis\SupervisionIndex::class)->name('index');
            Route::get('/{thesisId}', \App\Livewire\Lecturer\Thesis\GuidanceDetail::class)->name('guidance');
        });
    });

    Route::prefix('mhs')->name('student.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Student\Dashboard::class)->name('dashboard');
        // Tambah ini:
        Route::get('/krs', \App\Livewire\Student\Krs\KrsIndex::class)->name('krs');
        Route::get('/profile', \App\Livewire\Student\Profile::class)->name('profile');
        Route::get('/print/krs', [PrintController::class, 'printKrs'])->name('print.krs');
        Route::get('/print/khs', [PrintController::class, 'printKhs'])->name('print.khs');
        Route::get('/khs', \App\Livewire\Student\Khs\KhsIndex::class)->name('khs.index');
        Route::get('/transcript', \App\Livewire\Student\Transcript::class)->name('transcript');
        Route::get('/print/transcript', [PrintController::class, 'printTranscript'])->name('print.transcript');
        Route::get('/bills', \App\Livewire\Student\Finance\BillIndex::class)->name('bills');
        Route::get('/edom', \App\Livewire\Student\Lpm\EdomList::class)->name('edom.list');
        Route::get('/edom/fill/{classroomId}', \App\Livewire\Student\Lpm\EdomForm::class)->name('edom.fill');
        Route::get('/thesis-proposal', \App\Livewire\Student\Thesis\ThesisProposal::class)->name('thesis.proposal');
        Route::get('/thesis-log', \App\Livewire\Student\Thesis\ThesisLogIndex::class)->name('thesis.log');
        Route::get('/print/active-letter', [App\Http\Controllers\Student\PrintController::class, 'printActiveStudent'])->name('print.active');
        Route::get('/attendance', \App\Livewire\Student\Attendance\SubmitAttendance::class)->name('attendance');
        Route::get('/attendance/recap', \App\Livewire\Student\Attendance\AttendanceRecap::class)->name('attendance.recap');
        Route::get('/print/exam-card', [PrintController::class, 'printExamCard'])->name('print.exam-card');
        Route::get('/letters', \App\Livewire\Student\Letter\RequestIndex::class)->name('letters.index');
        Route::get('/print/letter/{id}', [PrintController::class, 'printLetter'])->name('print.letter');
    });

    Route::view('profile', 'profile')->name('profile');


    Route::prefix('pmb')->name('pmb.')->group(function () {
        Route::get('/register', \App\Livewire\Pmb\RegistrationWizard::class)->name('register');
        // Nanti buat halaman status di sini
        Route::get('/status', \App\Livewire\Pmb\StatusPage::class)->name('status');
        Route::get('/print-card', [\App\Http\Controllers\Pmb\PrintController::class, 'printCard'])->name('print.card');
        Route::get('/print-loa', [\App\Http\Controllers\Pmb\PrintController::class, 'printLoa'])->name('print.loa');
        Route::get('/exam', \App\Livewire\Pmb\CbtExam::class)->name('exam');
        Route::get('/pmb/payment', \App\Livewire\Pmb\PmbPayment::class)->name('payment'); // Rute Baru

    });
});

require __DIR__ . '/auth.php';
Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');
