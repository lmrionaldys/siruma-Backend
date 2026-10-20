<?php

namespace Database\Seeders;

use App\Models\Blok;
use App\Models\Perumahan;
use Illuminate\Database\Seeder;

class BlokSeeder extends Seeder
{
    public function run(): void
    {
        $perumahan = Perumahan::where(
            'nama_perumahan',
            'Perumahan H'
        )->firstOrFail();

        Blok::create([
            'perumahan_id' => $perumahan->id,
            'nama_blok' => 'A',
        ]);

        Blok::create([
            'perumahan_id' => $perumahan->id,
            'nama_blok' => 'B',
        ]);
    }
}