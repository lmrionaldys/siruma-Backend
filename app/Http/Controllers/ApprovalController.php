<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    // Menampilkan daftar pengajuan untuk petugas approval
    public function index(Request $request)
    {
        $user = $request->user();

        // Hanya Approval dan Super Admin yang boleh mengakses
        if (!in_array($user->role_id, [2, 3])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk melihat daftar approval.',
            ], 403);
        }

        $approvals = Approval::with([
            'monitoring.unit.blok.perumahan',
            'monitoring.user',
            'monitoring.penilaian',
            'monitoring.fotoMonitoring',
            'approver',
        ])
        ->latest()
        ->get();

        return response()->json([
            'message' => 'Daftar approval berhasil diambil.',
            'data' => $approvals,
        ]);
    }

    // Melihat detail satu pengajuan
    public function show(Request $request, $id)
    {
        $user = $request->user();

        if (!in_array($user->role_id, [2, 3])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk melihat detail approval.',
            ], 403);
        }

        $approval = Approval::with([
            'monitoring.unit.blok.perumahan',
            'monitoring.user',
            'monitoring.penilaian',
            'monitoring.fotoMonitoring',
            'approver',
        ])->findOrFail($id);

        return response()->json([
            'message' => 'Detail approval berhasil diambil.',
            'data' => $approval,
        ]);
    }
    
    // Menyetujui pengajuan monitoring
    public function approve(Request $request, $id)
    {
        $user = $request->user();

        if (!in_array($user->role_id, [2, 3])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menyetujui pengajuan.',
            ], 403);
        }

        $validated = $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        $approval = \App\Models\Approval::with('monitoring')
            ->findOrFail($id);

        if ($approval->status !== 'MENUNGGU') {
            return response()->json([
                'message' => 'Pengajuan ini sudah diproses.',
            ], 422);
        }

        $monitoring = $approval->monitoring;

        if ($monitoring->status !== 'DIAJUKAN') {
            return response()->json([
                'message' => 'Monitoring tidak dalam status DIAJUKAN.',
            ], 422);
        }

        $approval->update([
            'approval_by' => $user->id,
            'status' => 'DISETUJUI',
            'catatan' => $validated['catatan'] ?? null,
            'tanggal_approval' => now(),
        ]);

        $monitoring->update([
            'status' => 'DISETUJUI',
        ]);

        return response()->json([
            'message' => 'Pengajuan berhasil disetujui.',
            'data' => $approval->load('monitoring'),
        ]);
    }

    // Menolak pengajuan monitoring
    public function reject(Request $request, $id)
    {
        $user = $request->user();

        if (!in_array($user->role_id, [2, 3])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menolak pengajuan.',
            ], 403);
        }

        $validated = $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        $approval = \App\Models\Approval::with('monitoring')
            ->findOrFail($id);

        if ($approval->status !== 'MENUNGGU') {
            return response()->json([
                'message' => 'Pengajuan ini sudah diproses.',
            ], 422);
        }

        $monitoring = $approval->monitoring;

        if ($monitoring->status !== 'DIAJUKAN') {
            return response()->json([
                'message' => 'Monitoring tidak dalam status DIAJUKAN.',
            ], 422);
        }

        $approval->update([
            'approval_by' => $user->id,
            'status' => 'DITOLAK',
            'catatan' => $validated['catatan'],
            'tanggal_approval' => now(),
        ]);

        $monitoring->update([
            'status' => 'DITOLAK',
        ]);

        return response()->json([
            'message' => 'Pengajuan berhasil ditolak.',
            'data' => $approval->load('monitoring'),
        ]);
    }

}
