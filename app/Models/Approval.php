<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    protected $table = 'approval';

    protected $fillable = [
        'monitoring_id',
        'approval_by',
        'status',
        'catatan',
        'tanggal_approval',
    ];

    protected $casts = [
        'tanggal_approval' => 'datetime',
    ];

    public function monitoring(): BelongsTo
    {
        return $this->belongsTo(Monitoring::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_by');
    }
}