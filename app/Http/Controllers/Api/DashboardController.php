<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Challenge;
use Illuminate\Http\Request;

class DashboardController extends ApiController
{
    public function show(Request $request)
    {
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

        $articles = Article::with('category')
            ->latest('article_id')
            ->take(5)
            ->get()
            ->map(fn (Article $article) => $this->articleData($article))
            ->values();

        return $this->success('Dashboard berhasil diambil', [
            'user' => $this->userData($user),
            'challenges' => $challenges,
            'articles' => $articles,
        ]);
    }
}