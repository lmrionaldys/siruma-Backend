<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    // Menampilkan daftar monitoring milik pengguna yang login
    public function index(Request $request)
    {
        $monitorings = Monitoring::with(['unit.blok.perumahan', 'penilaian', 'fotoMonitoring'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Daftar monitoring berhasil diambil.',
            'data' => $monitorings,
        ]);
    }

    // Menampilkan detail satu monitoring
    public function show(Request $request, $id)
    {
        $monitoring = Monitoring::with(['unit.blok.perumahan', 'penilaian', 'fotoMonitoring'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'message' => 'Detail monitoring berhasil diambil.',
            'data' => $monitoring,
        ]);
    }

    // Membuat monitoring baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:unit,id',
            'tanggal_monitoring' => 'nullable|date',
            'progress' => 'nullable|numeric|min:0|max:100',
            'kondisi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $monitoring = Monitoring::create([
            'unit_id' => $validated['unit_id'],
            'user_id' => $request->user()->id,
            'tanggal_monitoring' => $validated['tanggal_monitoring']
                ?? now()->toDateString(),
            'progress' => $validated['progress'] ?? 0,
            'kondisi' => $validated['kondisi'],
            'keterangan' => $validated['keterangan'] ?? null,
            'status' => 'DRAFT',
        ]);

        return response()->json([
            'message' => 'Monitoring berhasil dibuat.',
            'data' => $monitoring,
        ], 201);
    }
}