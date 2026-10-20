<?php

namespace Database\Seeders;

use App\Models\Blok;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $blokA = Blok::where('nama_blok', 'A')->firstOrFail();
        $blokB = Blok::where('nama_blok', 'B')->firstOrFail();

        Unit::create([
            'blok_id' => $blokA->id,
            'nomor_unit' => 'A1',
            'luas_tanah' => 98,
            'luas_bangunan' => 36,
            'nilai_tanah_per_m2' => 1000000,
            'nilai_bangunan_per_m2' => 1500000,
            'status' => 'aktif',
        ]);

        Unit::create([
            'blok_id' => $blokA->id,
            'nomor_unit' => 'A2',
            'luas_tanah' => 97,
            'luas_bangunan' => 36,
            'nilai_tanah_per_m2' => 1000000,
            'nilai_bangunan_per_m2' => 1500000,
            'status' => 'aktif',
        ]);

        Unit::create([
            'blok_id' => $blokB->id,
            'nomor_unit' => 'B1',
            'luas_tanah' => 100,
            'luas_bangunan' => 36,
            'nilai_tanah_per_m2' => 1000000,
            'nilai_bangunan_per_m2' => 1500000,
            'status' => 'aktif',
        ]);
    }
}