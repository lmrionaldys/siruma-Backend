<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $approval = DB::transaction(function () use ($id, $user, $validated) {

            $approval = Approval::with('monitoring')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($approval->status !== 'MENUNGGU') {
                abort(422, 'Pengajuan ini sudah diproses.');
            }

            $monitoring = $approval->monitoring;

            if ($monitoring->status !== 'DIAJUKAN') {
                abort(422, 'Monitoring tidak dalam status DIAJUKAN.');
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

            return $approval->load('monitoring');
        });

        return response()->json([
            'message' => 'Pengajuan berhasil disetujui.',
            'data' => $approval,
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

        $approval = DB::transaction(function () use ($id, $user, $validated) {

            $approval = Approval::with('monitoring')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($approval->status !== 'MENUNGGU') {
                abort(422, 'Pengajuan ini sudah diproses.');
            }

            $monitoring = $approval->monitoring;

            if ($monitoring->status !== 'DIAJUKAN') {
                abort(422, 'Monitoring tidak dalam status DIAJUKAN.');
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

            return $approval->load('monitoring');
        });

        return response()->json([
            'message' => 'Pengajuan berhasil ditolak.',
            'data' => $approval,
        ]);
    }

}
