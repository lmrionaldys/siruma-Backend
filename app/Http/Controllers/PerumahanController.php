<?php

namespace App\Http\Controllers;

use App\Models\Perumahan;
use Illuminate\Http\Request;

class PerumahanController extends Controller
{
    // Memeriksa akses Super Admin
    private function cekSuperAdmin(Request $request)
    {
        if ($request->user()->role_id !== 3) {
            return response()->json([
                'message' => 'Akses hanya untuk Super Admin.',
            ], 403);
        }

        return null;
    }

    // Menampilkan seluruh perumahan
    public function index()
    {
        $perumahan = Perumahan::withCount('blok')->get();

        return response()->json([
            'message' => 'Daftar perumahan berhasil diambil.',
            'data' => $perumahan,
        ]);
    }

    // Menampilkan detail perumahan
    public function show($id)
    {
        $perumahan = Perumahan::with('blok')->findOrFail($id);

        return response()->json([
            'message' => 'Detail perumahan berhasil diambil.',
            'data' => $perumahan,
        ]);
    }

    // Menampilkan blok berdasarkan perumahan
    public function blok($id)
    {
        $perumahan = Perumahan::findOrFail($id);

        return response()->json([
            'message' => 'Daftar blok berhasil diambil.',
            'data' => $perumahan->blok,
        ]);
    }

    // Menambahkan perumahan
    public function store(Request $request)
    {
        if ($response = $this->cekSuperAdmin($request)) {
            return $response;
        }

        $validated = $request->validate([
            'nama_perumahan' => 'required|string|max:255',
            'lokasi' => 'nullable|string',
            'developer' => 'nullable|string|max:255',
            'status' => 'sometimes|in:aktif,nonaktif',
        ]);

        $perumahan = Perumahan::create($validated);

        return response()->json([
            'message' => 'Perumahan berhasil ditambahkan.',
            'data' => $perumahan,
        ], 201);
    }

    // Mengubah perumahan
    public function update(Request $request, $id)
    {
        if ($response = $this->cekSuperAdmin($request)) {
            return $response;
        }

        $perumahan = Perumahan::findOrFail($id);

        $validated = $request->validate([
            'nama_perumahan' => 'sometimes|required|string|max:255',
            'lokasi' => 'nullable|string',
            'developer' => 'nullable|string|max:255',
            'status' => 'sometimes|in:aktif,nonaktif',
        ]);

        $perumahan->update($validated);

        return response()->json([
            'message' => 'Perumahan berhasil diperbarui.',
            'data' => $perumahan,
        ]);
    }

    // Menghapus perumahan
    public function destroy(Request $request, $id)
    {
        if ($response = $this->cekSuperAdmin($request)) {
            return $response;
        }

        $perumahan = Perumahan::withCount('blok')->findOrFail($id);

        if ($perumahan->blok_count > 0) {
            return response()->json([
                'message' => 'Perumahan tidak dapat dihapus karena masih memiliki blok.',
            ], 422);
        }

        $perumahan->delete();

        return response()->json([
            'message' => 'Perumahan berhasil dihapus.',
        ]);
    }
}
