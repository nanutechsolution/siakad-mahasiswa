<?php

namespace App\Http\Controllers\Pmb;

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
}