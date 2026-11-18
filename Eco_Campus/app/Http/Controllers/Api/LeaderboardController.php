<?php

namespace App\Http\Controllers\Api;

use App\Services\LeaderboardService;
use Illuminate\Http\Request;

class LeaderboardController extends ApiController
{
    // GET /api/leaderboard?board=orang|jurusan|fakultas
    public function index(Request $request)
    {
        $board = LeaderboardService::valid($request->query('board'));

        $rows = LeaderboardService::get($board)->values()->map(fn ($r, $i) => [
            'rank' => $i + 1,
            'name' => $r->name,
            'sub' => $r->sub,
            'points' => $r->pts,
        ])->all();

        return $this->success('Leaderboard berhasil diambil', [
            'board' => $board,
            'rows' => $rows,
        ]);
    }
}