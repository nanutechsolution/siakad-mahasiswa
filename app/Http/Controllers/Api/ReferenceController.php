<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudyProgram;
use Illuminate\Http\JsonResponse;

class ReferenceController extends Controller
{
    public function getStudyPrograms(): JsonResponse
    {
        // Ambil data prodi dari DB SIAKAD
        // Select kolom yang penting saja
        $prodis = StudyProgram::select('id', 'code', 'name', 'degree', 'faculty_id')
            // ->where('is_active', true) // Pastikan hanya ambil yang aktif
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $prodis
        ]);
    }
}
