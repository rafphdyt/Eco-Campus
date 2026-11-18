<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $board = LeaderboardService::valid($request->query('board'));

        return view('admin.dashboard', [
            'totalStudents' => User::where('role', 'mahasiswa')->count(),
            'totalProofs' => UserChallenge::where('status', 'approved')->count(),
            'totalPoints' => User::where('role', 'mahasiswa')->sum('total_points'),
            'activeChallenges' => Challenge::available()->count(),
            'rows' => LeaderboardService::get($board),
            'board' => $board,
        ]);
    }
}