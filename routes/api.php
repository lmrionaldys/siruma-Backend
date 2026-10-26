<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerumahanController;
use App\Http\Controllers\BlokController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\FotoMonitoringController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/perumahan', [PerumahanController::class, 'index']);
    Route::get('/perumahan/{id}', [PerumahanController::class, 'show']);
    Route::get('/perumahan/{id}/blok', [PerumahanController::class, 'blok']);
    Route::get('/blok/{id}/unit', [BlokController::class, 'unit']);
    Route::get('/monitoring', [MonitoringController::class, 'index']);
    Route::get('/monitoring/{id}', [MonitoringController::class, 'show']);
    Route::post('/monitoring', [MonitoringController::class, 'store']);
    Route::get('/penilaian', [PenilaianController::class, 'index']);
    Route::get('/penilaian/{id}', [PenilaianController::class, 'show']);
    Route::post('/penilaian', [PenilaianController::class, 'store']);
    Route::get('/monitoring/{id}/foto',[FotoMonitoringController::class, 'index']);
    Route::post('/monitoring/{id}/foto',[FotoMonitoringController::class, 'store']);
});


