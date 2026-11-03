<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Monitoring;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    // Menampilkan daftar penilaian sesuai role
    public function index(Request $request)
    {
        $user = $request->user();

        if (!in_array($user->role_id, [1, 2, 3])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses.'
            ], 403);
        }

        $query = Penilaian::with(
            'monitoring.unit.blok.perumahan'
        );

        // User hanya melihat penilaian miliknya
        if ($user->role_id == 1) {
            $query->whereHas('monitoring', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $penilaian = $query->latest()->get();

        return response()->json([
            'message' => 'Daftar penilaian berhasil diambil.',
            'data' => $penilaian,
        ]);
    }

    // Menampilkan detail penilaian
    public function show(Request $request, $id)
    {
        $user = $request->user();

        if (!in_array($user->role_id, [1, 2, 3])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses.'
            ], 403);
        }

        $query = Penilaian::with(
            'monitoring.unit.blok.perumahan'
        );

        // Batasi User pada data miliknya sendiri
        if ($user->role_id == 1) {
            $query->whereHas('monitoring', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $penilaian = $query->findOrFail($id);

        return response()->json([
            'message' => 'Detail penilaian berhasil diambil.',
            'data' => $penilaian,
        ]);
    }

    // Membuat penilaian baru
    public function store(Request $request)
    {
        $user = $request->user();

        // Hanya User yang dapat membuat penilaian
        if ($user->role_id != 1) {
            return response()->json([
                'message' => 'Hanya User yang dapat membuat penilaian.'
            ], 403);
        }

        $validated = $request->validate([
            'monitoring_id' => [
                'required',
                'integer',
                'exists:monitoring,id',
                'unique:penilaian,monitoring_id',
            ],
            'luas_tanah' => 'required|numeric|min:0',
            'luas_bangunan' => 'required|numeric|min:0',
            'nilai_tanah_per_m2' => 'required|numeric|min:0',
            'nilai_bangunan_per_m2' => 'required|numeric|min:0',
        ]);

        // Pastikan monitoring milik User yang login
        $monitoring = Monitoring::where('user_id', $user->id)
            ->findOrFail($validated['monitoring_id']);

        // Penilaian hanya dapat dibuat ketika masih DRAFT
        if ($monitoring->status !== 'DRAFT') {
            return response()->json([
                'message' => 'Penilaian hanya dapat dibuat pada monitoring berstatus DRAFT.'
            ], 422);
        }

        $nilaiTotalTanah =
            $validated['luas_tanah'] *
            $validated['nilai_tanah_per_m2'];

        $nilaiTotalBangunan =
            $validated['luas_bangunan'] *
            $validated['nilai_bangunan_per_m2'];

        $penilaian = Penilaian::create([
            'monitoring_id' => $monitoring->id,
            'luas_tanah' => $validated['luas_tanah'],
            'luas_bangunan' => $validated['luas_bangunan'],
            'nilai_tanah_per_m2' => $validated['nilai_tanah_per_m2'],
            'nilai_bangunan_per_m2' => $validated['nilai_bangunan_per_m2'],
            'nilai_total_tanah' => $nilaiTotalTanah,
            'nilai_total_bangunan' => $nilaiTotalBangunan,
            'total_nilai' => $nilaiTotalTanah + $nilaiTotalBangunan,
        ]);

        return response()->json([
            'message' => 'Penilaian berhasil dibuat.',
            'data' => $penilaian,
        ], 201);
    }
}
