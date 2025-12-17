<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PmbSyncController;
use App\Http\Controllers\Api\ReferenceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Endpoint ini yang akan ditembak oleh PMB
Route::post('/v1/pmb/sync', [PmbSyncController::class, 'store']);
Route::get('/v1/ref/prodi', [ReferenceController::class, 'getStudyPrograms']);