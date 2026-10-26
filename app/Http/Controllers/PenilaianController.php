<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index()
    {
        $penilaian = Penilaian::with('monitoring.unit.blok.perumahan')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Daftar penilaian berhasil diambil.',
            'data' => $penilaian,
        ]);
    }

    public function show($id)
    {
        $penilaian = Penilaian::with('monitoring.unit.blok.perumahan')
            ->findOrFail($id);

        return response()->json([
            'message' => 'Detail penilaian berhasil diambil.',
            'data' => $penilaian,
        ]);
    }

    public function store(Request $request)
    {
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

        $nilaiTotalTanah =
            $validated['luas_tanah'] *
            $validated['nilai_tanah_per_m2'];

        $nilaiTotalBangunan =
            $validated['luas_bangunan'] *
            $validated['nilai_bangunan_per_m2'];

        $penilaian = Penilaian::create([
            'monitoring_id' => $validated['monitoring_id'],
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
