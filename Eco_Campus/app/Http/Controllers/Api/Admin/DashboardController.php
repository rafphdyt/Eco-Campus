<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;

class DashboardController extends ApiController
{
    // GET /api/admin  (opsional: ?board=orang|jurusan|fakultas)
    public function index(Request $request)
    {
        $board = LeaderboardService::valid($request->query('board'));

        $rows = LeaderboardService::get($board)->values()->map(fn ($r, $i) => [
            'rank' => $i + 1,
            'name' => $r->name,
            'sub' => $r->sub,
            'points' => $r->pts,
        ])->all();

        return $this->success('Data dashboard admin berhasil diambil', [
            'total_students' => User::where('role', 'mahasiswa')->count(),
            'total_proofs_approved' => UserChallenge::where('status', 'approved')->count(),
            'total_points' => (int) User::where('role', 'mahasiswa')->sum('total_points'),
            'active_challenges' => Challenge::available()->count(),
            'board' => $board,
            'leaderboard' => $rows,
        ]);
    }
}