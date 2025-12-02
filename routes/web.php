<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Student\PrintController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

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
        }
        return abort(403); // Role tidak dikenali
    })->name('dashboard');

    // 2. Dashboard ADMIN
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
        Route::get('/settings', \App\Livewire\Admin\Settings::class)->name('settings');
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/billings', \App\Livewire\Admin\Finance\BillingIndex::class)->name('billings');
            Route::get('/payments', \App\Livewire\Admin\Finance\PaymentVerification::class)->name('payments');
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
        });
    });

    Route::prefix('lecturer')->name('lecturer.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Lecturer\Dashboard::class)->name('dashboard');
        Route::get('/grading', \App\Livewire\Lecturer\GradingIndex::class)->name('grading.index');
        Route::get('/grading/{classId}', \App\Livewire\Lecturer\Grading::class)->name('grading');
        Route::get('/krs-validation', \App\Livewire\Lecturer\KrsValidation::class)->name('krs.validation');
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
    });

    Route::view('profile', 'profile')->name('profile');
});

require __DIR__ . '/auth.php';
Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');
