<?php

namespace App\Http\Controllers;

use App\Models\Blok;

class BlokController extends Controller
{
    public function unit($id)
    {
        $blok = Blok::findOrFail($id);

        return response()->json([
            'message' => 'Daftar unit berhasil diambil.',
            'data' => $blok->unit,
        ]);
    }
}
