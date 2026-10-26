<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\FotoMonitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FotoMonitoringController extends Controller
{
    // Mengambil daftar foto monitoring
    public function index(Request $request, $id)
    {
        $monitoring = Monitoring::where(
            'user_id',
            $request->user()->id
        )->findOrFail($id);

        return response()->json([
            'message' => 'Daftar foto berhasil diambil.',
            'data' => $monitoring->fotoMonitoring,
        ]);
    }

    // Mengunggah foto monitoring
    public function store(Request $request, $id)
    {
        $monitoring = Monitoring::where(
            'user_id',
            $request->user()->id
        )->findOrFail($id);

        $validated = $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'jenis_foto' => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $path = $request->file('foto')->store(
            'foto-monitoring',
            'public'
        );

        $foto = $monitoring->fotoMonitoring()->create([
            'jenis_foto' => $validated['jenis_foto'],
            'file_path' => $path,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return response()->json([
            'message' => 'Foto monitoring berhasil diunggah.',
            'data' => $foto,
            'url' => Storage::disk('public')->url($path),
        ], 201);
    }
}
