<?php

namespace App\Http\Controllers\Pmb;

use App\Enums\RegistrantStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Registrant;
use App\Models\Setting;

class PrintController extends Controller
{
    public function printCard()
    {
        $user = Auth::user();
        
        // Ambil data pendaftar milik user yang login
        $registrant = Registrant::with(['firstChoice', 'secondChoice'])
            ->where('user_id', $user->id)
            ->first();

        if (!$registrant) {
            return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        // Pastikan status sudah disubmit (bukan draft)
        if ($registrant->status == \App\Enums\RegistrantStatus::DRAFT) {
            return redirect()->back()->with('error', 'Silakan selesaikan pendaftaran terlebih dahulu.');
        }

        $setting = Setting::first();

        // Load PDF View
        $pdf = Pdf::loadView('pdf.pmb-card', [
            'registrant' => $registrant,
            'user' => $user,
            'setting' => $setting,
            'printed_at' => now()->format('d F Y H:i')
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Kartu_Peserta_' . $registrant->registration_no . '.pdf');
    }


      public function printLoa()
    {
        $user = Auth::user();
        
        $registrant = Registrant::with(['firstChoice']) // Kita butuh prodi yang diterima (biasanya pilihan 1)
            ->where('user_id', $user->id)
            ->first();

        // Validasi: Hanya yang LULUS yang boleh cetak
        if (!$registrant || $registrant->status !== RegistrantStatus::ACCEPTED) {
            return redirect()->route('pmb.status')->with('error', 'Dokumen ini hanya untuk peserta yang dinyatakan Lulus.');
        }

        $setting = Setting::first();

        $pdf = Pdf::loadView('pdf.pmb-loa', [
            'registrant' => $registrant,
            'user' => $user,
            'setting' => $setting,
            'date' => now()->format('d F Y')
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Surat_Kelulusan_' . $registrant->registration_no . '.pdf');
    }
}