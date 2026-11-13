<?php

namespace App\Http\Controllers;

use App\Models\Blok;
use App\Models\Perumahan;
use Illuminate\Http\Request;

class BlokController extends Controller
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

    // Menampilkan daftar blok berdasarkan perumahan
    public function index($perumahanId)
    {
        $perumahan = Perumahan::findOrFail($perumahanId);

        return response()->json([
            'message' => 'Daftar blok berhasil diambil.',
            'data' => $perumahan->blok()->withCount('unit')->get(),
        ]);
    }

    // Menampilkan detail blok
    public function show($id)
    {
        $blok = Blok::with('perumahan')
            ->withCount('unit')
            ->findOrFail($id);

        return response()->json([
            'message' => 'Detail blok berhasil diambil.',
            'data' => $blok,
        ]);
    }

    // Menambahkan blok
    public function store(Request $request, $perumahanId)
    {
        if ($response = $this->cekSuperAdmin($request)) {
            return $response;
        }

        Perumahan::findOrFail($perumahanId);

        $validated = $request->validate([
            'nama_blok' => 'required|string|max:255',
        ]);

        $blok = Blok::create([
            'perumahan_id' => $perumahanId,
            'nama_blok' => $validated['nama_blok'],
        ]);

        return response()->json([
            'message' => 'Blok berhasil ditambahkan.',
            'data' => $blok,
        ], 201);
    }

    // Mengubah nama blok
    public function update(Request $request, $id)
    {
        if ($response = $this->cekSuperAdmin($request)) {
            return $response;
        }

        $blok = Blok::findOrFail($id);

        $validated = $request->validate([
            'nama_blok' => 'sometimes|required|string|max:255',
        ]);

        $blok->update($validated);

        return response()->json([
            'message' => 'Blok berhasil diperbarui.',
            'data' => $blok,
        ]);
    }

    // Menghapus blok jika belum memiliki unit
    public function destroy(Request $request, $id)
    {
        if ($response = $this->cekSuperAdmin($request)) {
            return $response;
        }

        $blok = Blok::withCount('unit')->findOrFail($id);

        if ($blok->unit_count > 0) {
            return response()->json([
                'message' => 'Blok tidak dapat dihapus karena masih memiliki unit.',
            ], 422);
        }

        $blok->delete();

        return response()->json([
            'message' => 'Blok berhasil dihapus.',
        ]);
    }

    // Menampilkan unit berdasarkan blok
    public function unit($id)
    {
        $blok = Blok::findOrFail($id);

        return response()->json([
            'message' => 'Daftar unit berhasil diambil.',
            'data' => $blok->unit,
        ]);
    }
}
