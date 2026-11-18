<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\UserChallenge;
use Illuminate\Support\Facades\DB;

class ProofController extends ApiController
{
    // GET /api/admin/proofs
    public function index()
    {
        $data = UserChallenge::with(['user', 'challenge'])
            ->orderByRaw("status = 'pending' desc")
            ->orderByDesc('id')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'student' => $p->user?->name,
                'npm' => $p->user?->npm,
                'challenge' => $p->challenge?->title,
                'points' => $p->points,
                'status' => $p->status,
                'proof_url' => asset('storage/' . $p->proof_url),
                'submitted_at' => $p->submitted_at?->toDateTimeString(),
            ])->all();

        return $this->success('Data bukti berhasil diambil', $data);
    }

    // PATCH /api/admin/proofs/{proof}/approve
    public function approve(UserChallenge $proof)
    {
        if ($proof->status !== 'pending') {
            return $this->error('Bukti ini sudah diproses', 409);
        }

        DB::transaction(function () use ($proof) {
            $proof->update(['status' => 'approved', 'approved_at' => now()]);
            $proof->user->increment('total_points', $proof->points);
        });

        return $this->success('Bukti disetujui, poin ditambahkan', [
            'id' => $proof->id,
            'status' => $proof->status,
            'points_added' => $proof->points,
        ]);
    }

    // PATCH /api/admin/proofs/{proof}/reject
    public function reject(UserChallenge $proof)
    {
        if ($proof->status === 'rejected') {
            return $this->error('Bukti ini sudah ditolak', 409);
        }

        DB::transaction(function () use ($proof) {
            if ($proof->status === 'approved') {
                $user = $proof->user;
                $user->decrement('total_points', min($proof->points, $user->total_points));
            }
            $proof->update(['status' => 'rejected', 'approved_at' => null]);
        });

        return $this->success('Bukti ditolak', [
            'id' => $proof->id,
            'status' => $proof->status,
        ]);
    }
}