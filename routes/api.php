<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerumahanController;
use App\Http\Controllers\BlokController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\FotoMonitoringController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\UnitController;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    // PERUMAHAN
    Route::get('/perumahan', [PerumahanController::class, 'index']);
    Route::get('/perumahan/{id}', [PerumahanController::class, 'show']);
    Route::get('/perumahan/{id}/blok', [BlokController::class, 'index']);

    Route::post('/perumahan', [PerumahanController::class, 'store']);
    Route::put('/perumahan/{id}', [PerumahanController::class, 'update']);
    Route::delete('/perumahan/{id}', [PerumahanController::class, 'destroy']);

    // BLOK
    Route::post('/perumahan/{perumahanId}/blok', [BlokController::class, 'store']);
    Route::get('/blok/{id}', [BlokController::class, 'show']);
    Route::put('/blok/{id}', [BlokController::class, 'update']);
    Route::delete('/blok/{id}', [BlokController::class, 'destroy']);

    // UNIT
    Route::get('/blok/{blokId}/unit', [UnitController::class, 'index']);
    Route::post('/blok/{blokId}/unit', [UnitController::class, 'store']);
    Route::get('/unit/{id}', [UnitController::class, 'show']);
    Route::put('/unit/{id}', [UnitController::class, 'update']);
    Route::delete('/unit/{id}', [UnitController::class, 'destroy']);

    // MONITORING
    Route::get('/monitoring', [MonitoringController::class, 'index']);
    Route::get('/monitoring/{id}', [MonitoringController::class, 'show']);
    Route::post('/monitoring', [MonitoringController::class, 'store']);

    // PENILAIAN
    Route::get('/penilaian', [PenilaianController::class, 'index']);
    Route::get('/penilaian/{id}', [PenilaianController::class, 'show']);
    Route::post('/penilaian', [PenilaianController::class, 'store']);

    // FOTO MONITORING
    Route::get('/monitoring/{id}/foto', [FotoMonitoringController::class, 'index']);
    Route::post('/monitoring/{id}/foto', [FotoMonitoringController::class, 'store']);

    // APPROVAL
    Route::get('/approval', [ApprovalController::class, 'index']);
    Route::get('/approval/{id}', [ApprovalController::class, 'show']);
    Route::post('/monitoring/{id}/ajukan', [MonitoringController::class, 'submit']);
    Route::post('/approval/{id}/setujui', [ApprovalController::class, 'approve']);
    Route::post('/approval/{id}/tolak', [ApprovalController::class, 'reject']);
});
