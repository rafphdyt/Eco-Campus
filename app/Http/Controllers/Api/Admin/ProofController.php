<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use App\Models\UserChallenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProofController extends ApiController
{
    public function index(Request $request)
    {
        $filters = $this->check($request, [
            'status' => ['sometimes', 'in:all,pending,approved,rejected'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        $proofs = UserChallenge::with(['user.department', 'challenge'])
            ->when(
                isset($filters['status']) && $filters['status'] !== 'all',
                fn ($query) => $query->where('status', $filters['status'])
            )
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest('id')
            ->paginate((int) ($filters['per_page'] ?? 20));

        return $this->success('Daftar bukti berhasil diambil', [
            'proofs' => $proofs->getCollection()
                ->map(fn (UserChallenge $proof) => $this->proofData($proof))
                ->values(),
            'pagination' => $this->paginationData($proofs),
        ]);
    }

    public function approve(UserChallenge $proof)
    {
        $approved = DB::transaction(function () use ($proof) {
            $lockedProof = UserChallenge::query()
                ->lockForUpdate()
                ->findOrFail($proof->getKey());

            if ($lockedProof->status !== 'pending') {
                return false;
            }

            $lockedProof->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
            User::query()->whereKey($lockedProof->user_id)
                ->lockForUpdate()
                ->firstOrFail()
                ->increment('total_points', $lockedProof->points);

            return true;
        });

        if (! $approved) {
            return $this->error('Bukti ini sudah diproses', 409);
        }

        return $this->success('Bukti disetujui dan poin ditambahkan');
    }

    public function reject(UserChallenge $proof)
    {
        DB::transaction(function () use ($proof) {
            $lockedProof = UserChallenge::query()
                ->lockForUpdate()
                ->findOrFail($proof->getKey());

            if ($lockedProof->status === 'approved') {
                $user = User::query()
                    ->whereKey($lockedProof->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();
                $user->decrement(
                    'total_points',
                    min((int) $lockedProof->points, (int) $user->total_points)
                );
            }

            if ($lockedProof->status !== 'rejected') {
                $lockedProof->update([
                    'status' => 'rejected',
                    'approved_at' => null,
                ]);
            }
        });

        return $this->success('Bukti ditolak');
    }

    private function proofData(UserChallenge $proof): array
    {
        return [
            'submission_id' => $proof->getKey(),
            'status' => $proof->status,
            'submitted_at' => $proof->submitted_at,
            'approved_at' => $proof->approved_at,
            'points' => $proof->points,
            'proof_url' => Storage::disk('public')->url($proof->proof_url),
            'user' => $proof->user ? [
                'user_id' => $proof->user->user_id,
                'name' => $proof->user->name,
                'npm' => $proof->user->npm,
                'email' => $proof->user->email,
                'department' => $proof->user->department?->name,
            ] : null,
            'challenge' => $proof->challenge
                ? $this->challengeData($proof->challenge)
                : null,
        ];
    }
}