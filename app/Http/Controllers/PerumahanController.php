<?php

namespace App\Http\Controllers;

use App\Models\Perumahan;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerumahanController extends Controller
{
    public function __construct(
        private AuditLogService $auditLogService
    ) {}

    // Memeriksa akses Super Admin
    private function cekSuperAdmin(Request $request)
    {
        if ((int) $request->user()->role_id !== 3) {
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

        $perumahan = DB::transaction(function () use ($request, $validated) {
            $perumahan = Perumahan::create($validated);

            $this->auditLogService->catat(
                $request->user()->id,
                'Tambah Perumahan',
                'perumahan',
                $perumahan->id,
                'Menambahkan perumahan: ' . $perumahan->nama_perumahan
            );

            return $perumahan;
        });

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

        if (empty($validated)) {
            return response()->json([
                'message' => 'Tidak ada data yang dikirim untuk diperbarui.',
            ], 422);
        }

        DB::transaction(function () use ($request, $perumahan, $validated) {
            $perumahan->update($validated);

            $this->auditLogService->catat(
                $request->user()->id,
                'Edit Perumahan',
                'perumahan',
                $perumahan->id,
                'Memperbarui data perumahan: ' . $perumahan->nama_perumahan
            );
        });

        return response()->json([
            'message' => 'Perumahan berhasil diperbarui.',
            'data' => $perumahan->fresh(),
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

        DB::transaction(function () use ($request, $perumahan) {
            $namaPerumahan = $perumahan->nama_perumahan;
            $perumahanId = $perumahan->id;

            $perumahan->delete();

            $this->auditLogService->catat(
                $request->user()->id,
                'Hapus Perumahan',
                'perumahan',
                $perumahanId,
                'Menghapus perumahan: ' . $namaPerumahan
            );
        });

        return response()->json([
            'message' => 'Perumahan berhasil dihapus.',
        ]);
    }
}
