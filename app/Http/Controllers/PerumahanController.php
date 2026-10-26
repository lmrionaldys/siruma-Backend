<?php

namespace App\Http\Controllers;

use App\Models\Perumahan;

class PerumahanController extends Controller
{
    public function index()
    {
        $perumahan = Perumahan::withCount('blok')->get();

        return response()->json([
            'message' => 'Daftar perumahan berhasil diambil.',
            'data' => $perumahan,
        ]);
    }

    public function show($id)
    {
        $perumahan = Perumahan::with('blok')->findOrFail($id);

        return response()->json([
            'message' => 'Detail perumahan berhasil diambil.',
            'data' => $perumahan,
        ]);
    }

    public function blok($id)
    {
        $perumahan = Perumahan::findOrFail($id);

        return response()->json([
            'message' => 'Daftar blok berhasil diambil.',
            'data' => $perumahan->blok,
        ]);
    }
}
