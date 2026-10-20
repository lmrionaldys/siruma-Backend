<?php

namespace Database\Seeders;

use App\Models\Perumahan;
use Illuminate\Database\Seeder;

class PerumahanSeeder extends Seeder
{
    public function run(): void
    {
        Perumahan::create([
            'nama_perumahan' => 'Perumahan H',
            'lokasi' => 'Makassar',
            'developer' => 'Developer H',
            'status' => 'aktif',
        ]);
    }
}