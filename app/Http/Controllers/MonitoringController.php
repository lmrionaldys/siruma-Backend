<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\Approval;
use Illuminate\Support\Facades\DB;
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
        if ($request->user()->role_id !== 1) {
            return response()->json([
                'message' => 'Hanya User yang dapat membuat monitoring.',
            ], 403);
        }
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
    // Mengajukan monitoring untuk proses approval
    public function submit(Request $request, $id)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json([
                'message' => 'Hanya User yang dapat mengajukan monitoring.',
            ], 403);
        }

        $monitoring = Monitoring::where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($monitoring->status !== 'DRAFT') {
            return response()->json([
                'message' => 'Hanya monitoring berstatus DRAFT yang dapat diajukan.',
            ], 422);
        }

        if (!$monitoring->penilaian) {
            return response()->json([
                'message' => 'Buat penilaian terlebih dahulu sebelum mengajukan approval.',
            ], 422);
        }

        if ($monitoring->fotoMonitoring()->count() === 0) {
            return response()->json([
                'message' => 'Unggah minimal satu foto sebelum mengajukan approval.',
            ], 422);
        }

        $approval = DB::transaction(function () use ($monitoring) {
            $monitoring->update([
                'status' => 'DIAJUKAN',
            ]);

            return Approval::create([
                'monitoring_id' => $monitoring->id,
                'status' => 'MENUNGGU',
            ]);
        });

        return response()->json([
            'message' => 'Monitoring berhasil diajukan untuk approval.',
            'data' => $approval->load([
                'monitoring.unit.blok.perumahan',
                'monitoring.penilaian',
                'monitoring.fotoMonitoring',
            ]),
        ], 201);
    }


}