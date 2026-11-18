<?php

namespace App\Http\Controllers\Api;

use App\Models\Challenge;
use App\Models\UserChallenge;
use Illuminate\Http\Request;

class ChallengeController extends ApiController
{
    // GET /api/challenges
    public function index(Request $request)
    {
        $user = $request->user();
        $done = $user->doneTodayIds();
        $pending = $user->pendingTodayIds();

        $data = Challenge::available()->get()->map(fn ($c) =>
            $c->only(['challenge_id', 'title', 'description', 'points']) + [
                'status_hari_ini' => in_array($c->challenge_id, $done) ? 'approved'
                    : (in_array($c->challenge_id, $pending) ? 'pending' : 'belum'),
            ])->all();

        return $this->success('Data tantangan berhasil diambil', $data);
    }

    // POST /api/challenges/{challenge}/upload   (form-data: proof = file gambar)
    public function upload(Request $request, Challenge $challenge)
    {
        if (! $challenge->isAvailable()) {
            return $this->error('Tantangan tidak ditemukan atau sudah tidak aktif', 404);
        }

        $this->check($request, ['proof' => ['required', 'image', 'max:2048']], [
            'proof.required' => 'Bukti foto wajib diunggah',
            'proof.image' => 'Bukti harus berupa gambar',
            'proof.max' => 'Ukuran bukti maksimal 2 MB',
        ]);

        $user = $request->user();

        $sudah = $user->userChallenges()
            ->where('challenge_id', $challenge->challenge_id)
            ->whereIn('status', ['pending', 'approved'])
            ->whereDate('submitted_at', today())
            ->exists();

        if ($sudah) {
            return $this->error('Bukti untuk tantangan ini sudah dikirim hari ini', 409);
        }

        $path = $request->file('proof')->store('proofs', 'public');

        $uc = UserChallenge::create([
            'user_id' => $user->user_id,
            'challenge_id' => $challenge->challenge_id,
            'proof_url' => $path,
            'submitted_at' => now(),
            'status' => 'pending',
            'approved_at' => null,
            'points' => $challenge->points,
        ]);

        return $this->success('Bukti terkirim. Poin ditambahkan setelah admin memverifikasi', [
            'id' => $uc->id,
            'challenge_id' => $uc->challenge_id,
            'status' => $uc->status,
            'points' => $uc->points,
            'proof_url' => asset('storage/' . $uc->proof_url),
        ], 201);
    }
}