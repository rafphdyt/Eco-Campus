<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\UserChallenge;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChallengeController extends Controller
{
    public function index(Request $request)
    {
        $board = LeaderboardService::valid($request->query('board'));

        return view('challenges', [
            'challenges' => Challenge::where('status', 'active')->get(),
            'doneIds' => auth()->user()->doneTodayIds(),
            'rows' => LeaderboardService::get($board),
            'board' => $board,
        ]);
    }

    public function upload(Request $request, Challenge $challenge)
    {
        abort_if($challenge->status !== 'active', 404);
        $request->validate(['proof' => ['required', 'image', 'max:2048']]);

        $user = $request->user();

        $sudah = $user->userChallenges()
            ->where('challenge_id', $challenge->challenge_id)
            ->whereIn('status', ['pending', 'approved'])
            ->whereDate('submitted_at', today())
            ->exists();

        if ($sudah) {
            return back()->with('error', 'Bukti untuk tantangan ini sudah dikirim hari ini.');
        }

        $path = $request->file('proof')->store('proofs', 'public');

        UserChallenge::create([
            'user_id' => $user->user_id,
            'challenge_id' => $challenge->challenge_id,
            'proof_url' => $path,
            'submitted_at' => now(),
            'status' => 'pending',
            'approved_at' => null,
            'points' => $challenge->points,
        ]);

        return back()->with('success', 'Bukti terkirim. Poin ditambahkan setelah admin memverifikasi.');
    }
}