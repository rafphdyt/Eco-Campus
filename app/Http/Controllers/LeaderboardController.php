<?php

namespace App\Http\Controllers\Api;

use App\Services\LeaderboardService;
use Illuminate\Http\Request;

class LeaderboardController extends ApiController
{
    public function index(Request $request)
    {
        $board = LeaderboardService::valid($request->query('board'));

        return $this->success('Papan peringkat berhasil diambil', [
            'type' => $board,
            'rows' => $this->leaderboardData($board),
        ]);
    }
}