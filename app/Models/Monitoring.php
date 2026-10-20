<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Monitoring extends Model
{
    protected $table = 'monitoring';

    protected $fillable = [
        'unit_id',
        'user_id',
        'tanggal_monitoring',
        'progress',
        'kondisi',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_monitoring' => 'date',
        'progress' => 'decimal:2',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function penilaian(): HasOne
    {
        return $this->hasOne(Penilaian::class);
    }

    public function fotoMonitoring(): HasMany
    {
    return $this->hasMany(FotoMonitoring::class);
    }

    public function approval(): HasMany
    {
    return $this->hasMany(Approval::class);
    }
}