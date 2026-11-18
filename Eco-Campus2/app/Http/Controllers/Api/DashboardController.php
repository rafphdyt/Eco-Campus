<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Challenge;
use Illuminate\Http\Request;

class DashboardController extends ApiController
{
    // GET /api/dashboard
    public function index(Request $request)
    {
        $user = $request->user();
        $done = $user->doneTodayIds();
        $pending = $user->pendingTodayIds();

        $challenges = Challenge::available()->get()->map(fn ($c) =>
            $c->only(['challenge_id', 'title', 'description', 'points']) + [
                'status_hari_ini' => in_array($c->challenge_id, $done) ? 'approved'
                    : (in_array($c->challenge_id, $pending) ? 'pending' : 'belum'),
            ])->all();

        $articles = Article::with('category')->latest('article_id')->take(5)->get()->map(fn ($a) =>
            $a->only(['article_id', 'title', 'description', 'author', 'url', 'published_at'])
            + ['category' => $a->category?->name])->all();

        return $this->success('Data dashboard berhasil diambil', [
            'name' => $user->name,
            'total_points' => $user->total_points,
            'challenges' => $challenges,
            'articles' => $articles,
        ]);
    }
}