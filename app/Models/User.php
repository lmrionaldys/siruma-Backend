<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password', 'role_id', 'nama', 'username', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public function role(): BelongsTo
    {
    return $this->belongsTo(Role::class);
    }

    public function monitorings(): HasMany
    {
    return $this->hasMany(Monitoring::class);
    }

    public function approvals(): HasMany
    {
    return $this->hasMany(Approval::class, 'approval_by');
    }

    public function auditLogs(): HasMany
    {
    return $this->hasMany(AuditLog::class);
    }

    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
