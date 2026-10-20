<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penilaian extends Model
{
    protected $table = 'penilaian';

    protected $fillable = [
        'monitoring_id',
        'luas_tanah',
        'luas_bangunan',
        'nilai_tanah_per_m2',
        'nilai_bangunan_per_m2',
        'nilai_total_tanah',
        'nilai_total_bangunan',
        'total_nilai',
    ];

    protected $casts = [
        'luas_tanah' => 'decimal:2',
        'luas_bangunan' => 'decimal:2',
        'nilai_tanah_per_m2' => 'decimal:2',
        'nilai_bangunan_per_m2' => 'decimal:2',
        'nilai_total_tanah' => 'decimal:2',
        'nilai_total_bangunan' => 'decimal:2',
        'total_nilai' => 'decimal:2',
    ];

    public function monitoring(): BelongsTo
    {
        return $this->belongsTo(Monitoring::class);
    }
}