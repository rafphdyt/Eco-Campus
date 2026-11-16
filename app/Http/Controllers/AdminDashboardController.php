<?php

namespace App\Http\Controllers\Api;

use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;

class AdminDashboardController extends ApiController
{
    public function show(Request $request)
    {
        $board = LeaderboardService::valid($request->query('board'));

        return $this->success('Dashboard admin berhasil diambil', [
            'total_students' => User::where('role', 'mahasiswa')->count(),
            'approved_proofs' => UserChallenge::where('status', 'approved')->count(),
            'total_points' => (int) User::where('role', 'mahasiswa')->sum('total_points'),
            'active_challenges' => Challenge::where('status', 'active')->count(),
            'leaderboard_type' => $board,
            'leaderboard' => $this->leaderboardData($board),
        ]);
    }
}