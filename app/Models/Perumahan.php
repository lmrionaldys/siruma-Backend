<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perumahan extends Model
{
    protected $table = 'perumahan';

    protected $fillable = [
        'nama_perumahan',
        'lokasi',
        'developer',
        'status',
    ];

    public function blok(): HasMany
    {
        return $this->hasMany(Blok::class);
    }
}