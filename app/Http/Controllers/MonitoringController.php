<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\Approval;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    // Menampilkan daftar monitoring sesuai role
    public function index(Request $request)
    {
        $user = $request->user();

        // Hanya User dan Super Admin yang dapat mengakses
        if (!in_array((int) $user->role_id, [1, 3])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk melihat daftar monitoring.',
            ], 403);
        }

        $query = Monitoring::with([
            'unit.blok.perumahan',
            'penilaian',
            'fotoMonitoring',
        ]);

        // User hanya melihat monitoring miliknya
        if ((int) $user->role_id === 1) {
            $query->where('user_id', $user->id);
        }

        $monitorings = $query->latest()->get();

        return response()->json([
            'message' => 'Daftar monitoring berhasil diambil.',
            'data' => $monitorings,
        ]);
    }
    // Menampilkan detail monitoring sesuai role
    public function show(Request $request, $id)
    {
        $user = $request->user();

        // Hanya User dan Super Admin yang dapat mengakses
        if (!in_array((int) $user->role_id, [1, 3])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk melihat detail monitoring.',
            ], 403);
        }

        $query = Monitoring::with([
            'unit.blok.perumahan',
            'penilaian',
            'fotoMonitoring',
        ]);

        // User hanya dapat melihat monitoring miliknya
        if ((int) $user->role_id === 1) {
            $query->where('user_id', $user->id);
        }

        $monitoring = $query->findOrFail($id);

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