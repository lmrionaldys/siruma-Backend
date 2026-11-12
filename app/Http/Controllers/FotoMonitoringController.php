<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FotoMonitoringController extends Controller
{
    // Mengambil daftar foto monitoring
    public function index(Request $request, $id)
    {
        $user = $request->user();

        // User hanya dapat melihat foto miliknya
        if ($user->role_id == 1) {
            $monitoring = Monitoring::where('user_id', $user->id)
                ->findOrFail($id);
        }

        // Approval hanya dapat melihat foto pengajuan
        elseif ($user->role_id == 2) {
            $monitoring = Monitoring::where('status', 'DIAJUKAN')
                ->findOrFail($id);
        }

        // Super Admin dapat melihat semua foto
        elseif ($user->role_id == 3) {
            $monitoring = Monitoring::findOrFail($id);
        }

        else {
            return response()->json([
                'message' => 'Anda tidak memiliki akses.'
            ], 403);
        }

        return response()->json([
            'message' => 'Daftar foto berhasil diambil.',
            'data' => $monitoring->fotoMonitoring,
        ]);
    }

    // Mengunggah foto monitoring
    public function store(Request $request, $id)
    {
        $user = $request->user();

        // Hanya User yang boleh mengunggah foto
        if ($user->role_id != 1) {
            return response()->json([
                'message' => 'Hanya User yang dapat mengunggah foto monitoring.'
            ], 403);
        }

        $monitoring = Monitoring::where('user_id', $user->id)
            ->findOrFail($id);

        // Foto hanya boleh ditambahkan saat DRAFT
        if ($monitoring->status != 'DRAFT') {
            return response()->json([
                'message' => 'Foto hanya dapat ditambahkan pada monitoring berstatus DRAFT.'
            ], 422);
        }

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
