<?php

namespace App\Http\Controllers\Api;

use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChallengeController extends ApiController
{
    public function index(Request $request)
    {
        $board = LeaderboardService::valid($request->query('board'));
        $user = $request->user();
        $todayStatuses = $user->userChallenges()
            ->whereDate('submitted_at', today())
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->keyBy('challenge_id');

        $challenges = Challenge::query()
            ->where('status', 'active')
            ->orderBy('challenge_id')
            ->get()
            ->map(fn (Challenge $challenge) => $this->challengeData(
                $challenge,
                $todayStatuses->get($challenge->challenge_id)?->status
            ))
            ->values();

        return $this->success('Tantangan berhasil diambil', [
            'challenges' => $challenges,
            'leaderboard_type' => $board,
            'leaderboard' => $this->leaderboardData($board),
        ]);
    }

    public function submit(Request $request, Challenge $challenge)
    {
        if ($challenge->status !== 'active') {
            return $this->error('Tantangan tidak aktif', 404);
        }

        $this->check($request, [
            'proof' => ['required', 'image', 'max:2048'],
        ], [
            'proof.required' => 'Foto bukti wajib diunggah',
            'proof.image' => 'Bukti harus berupa gambar',
            'proof.max' => 'Ukuran foto maksimal 2 MB',
        ]);

        $user = $request->user();
        $path = $request->file('proof')->store('proofs', 'public');

        try {
            $submission = DB::transaction(function () use ($user, $challenge, $path) {
                User::query()->whereKey($user->user_id)->lockForUpdate()->firstOrFail();

                $alreadySubmitted = $user->userChallenges()
                    ->where('challenge_id', $challenge->challenge_id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->whereDate('submitted_at', today())
                    ->exists();

                if ($alreadySubmitted) {
                    return null;
                }

                return UserChallenge::create([
                    'user_id' => $user->user_id,
                    'challenge_id' => $challenge->challenge_id,
                    'proof_url' => $path,
                    'submitted_at' => now(),
                    'status' => 'pending',
                    'approved_at' => null,
                    'points' => $challenge->points,
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        if (! $submission) {
            Storage::disk('public')->delete($path);

            return $this->error('Bukti untuk tantangan ini sudah dikirim hari ini', 409);
        }

        return $this->success('Bukti terkirim. Poin ditambahkan setelah admin memverifikasi.', [
            'submission_id' => $submission->getKey(),
            'challenge_id' => $submission->challenge_id,
            'status' => $submission->status,
            'submitted_at' => $submission->submitted_at,
            'proof_url' => Storage::disk('public')->url($submission->proof_url),
            'points' => $submission->points,
        ], 201);
    }

    public function history(Request $request)
    {
        $filters = $this->check($request, [
            'status' => ['sometimes', 'in:pending,approved,rejected'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        $submissions = $request->user()->userChallenges()
            ->with('challenge')
            ->when(isset($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->latest('submitted_at')
            ->paginate((int) ($filters['per_page'] ?? 15));

        return $this->success('Riwayat tantangan berhasil diambil', [
            'submissions' => $submissions->getCollection()->map(function (UserChallenge $submission) {
                return [
                    'submission_id' => $submission->getKey(),
                    'challenge' => $submission->challenge
                        ? $this->challengeData($submission->challenge)
                        : null,
                    'proof_url' => Storage::disk('public')->url($submission->proof_url),
                    'status' => $submission->status,
                    'submitted_at' => $submission->submitted_at,
                    'approved_at' => $submission->approved_at,
                    'points' => $submission->points,
                ];
            })->values(),
            'pagination' => $this->paginationData($submissions),
        ]);
    }
}