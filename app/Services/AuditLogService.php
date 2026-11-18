<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogService
{
    public function catat(
        int $userId,
        string $aktivitas,
        ?string $tabel = null,
        ?int $recordId = null,
        ?string $keterangan = null
    ): void {
        AuditLog::create([
            'user_id' => $userId,
            'aktivitas' => $aktivitas,
            'tabel' => $tabel,
            'record_id' => $recordId,
            'keterangan' => $keterangan,
        ]);
    }
}
