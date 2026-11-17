<?php

namespace App\Http\Controllers;

use App\Models\Blok;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    private function cekSuperAdmin(Request $request)
    {
        if ($request->user()->role_id !== 3) {
            return response()->json([
                'message' => 'Akses hanya untuk Super Admin.',
            ], 403);
        }

        return null;
    }

    // Menampilkan daftar unit berdasarkan blok
    public function index($blokId)
    {
        $blok = Blok::findOrFail($blokId);

        return response()->json([
            'message' => 'Daftar unit berhasil diambil.',
            'data' => $blok->unit,
        ]);
    }

    // Menampilkan detail unit
    public function show($id)
    {
        $unit = Unit::with('blok.perumahan')
            ->withCount('monitorings')
            ->findOrFail($id);

        return response()->json([
            'message' => 'Detail unit berhasil diambil.',
            'data' => $unit,
        ]);
    }

    // Menambahkan unit
    public function store(Request $request, $blokId)
    {
        if ($response = $this->cekSuperAdmin($request)) {
            return $response;
        }

        Blok::findOrFail($blokId);

        $validated = $request->validate([
            'nomor_unit' => 'required|string|max:255',
            'luas_tanah' => 'required|numeric|min:0',
            'luas_bangunan' => 'required|numeric|min:0',
            'nilai_tanah_per_m2' => 'required|numeric|min:0',
            'nilai_bangunan_per_m2' => 'required|numeric|min:0',
            'status' => 'sometimes|in:aktif,nonaktif',
        ]);

        $unit = Unit::create([
            'blok_id' => $blokId,
            ...$validated,
        ]);

        return response()->json([
            'message' => 'Unit berhasil ditambahkan.',
            'data' => $unit,
        ], 201);
    }

    // Mengubah data unit
    public function update(Request $request, $id)
    {
        if ($response = $this->cekSuperAdmin($request)) {
            return $response;
        }

        $unit = Unit::findOrFail($id);

        $validated = $request->validate([
            'nomor_unit' => 'sometimes|required|string|max:255',
            'luas_tanah' => 'sometimes|required|numeric|min:0',
            'luas_bangunan' => 'sometimes|required|numeric|min:0',
            'nilai_tanah_per_m2' => 'sometimes|required|numeric|min:0',
            'nilai_bangunan_per_m2' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|in:aktif,nonaktif',
        ]);

        $unit->update($validated);

        return response()->json([
            'message' => 'Unit berhasil diperbarui.',
            'data' => $unit,
        ]);
    }

    // Menghapus unit jika belum memiliki riwayat monitoring
    public function destroy(Request $request, $id)
    {
        if ($response = $this->cekSuperAdmin($request)) {
            return $response;
        }

        $unit = Unit::withCount('monitorings')->findOrFail($id);

        if ($unit->monitorings_count > 0) {
            return response()->json([
                'message' => 'Unit tidak dapat dihapus karena masih memiliki riwayat monitoring.',
            ], 422);
        }

        $unit->delete();

        return response()->json([
            'message' => 'Unit berhasil dihapus.',
        ]);
    }
}
