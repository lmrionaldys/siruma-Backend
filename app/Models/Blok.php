<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Blok extends Model
{
    protected $table = 'blok';

    protected $fillable = [
        'perumahan_id',
        'nama_blok',
    ];

    public function perumahan(): BelongsTo
    {
        return $this->belongsTo(Perumahan::class);
    }

    public function unit(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}