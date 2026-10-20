<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $table = 'unit';

    protected $fillable = [
        'blok_id',
        'nomor_unit',
        'luas_tanah',
        'luas_bangunan',
        'nilai_tanah_per_m2',
        'nilai_bangunan_per_m2',
        'status',
    ];

    protected $casts = [
        'luas_tanah' => 'decimal:2',
        'luas_bangunan' => 'decimal:2',
        'nilai_tanah_per_m2' => 'decimal:2',
        'nilai_bangunan_per_m2' => 'decimal:2',
    ];

    public function blok(): BelongsTo
    {
        return $this->belongsTo(Blok::class);
    }

    public function monitorings(): HasMany
    {
        return $this->hasMany(Monitoring::class);
    }
}