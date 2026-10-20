<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoMonitoring extends Model
{
    protected $table = 'foto_monitoring';

    protected $fillable = [
        'monitoring_id',
        'jenis_foto',
        'file_path',
        'keterangan',
    ];

    public function monitoring(): BelongsTo
    {
        return $this->belongsTo(Monitoring::class);
    }
}