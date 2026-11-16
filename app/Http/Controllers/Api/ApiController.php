<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Challenge;
use App\Models\User;
use App\Services\LeaderboardService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class ApiController extends Controller
{
    protected function success(string $message, mixed $data = null, int $code = 200): JsonResponse
    {
        $body = ['status' => 'success', 'message' => $message];
        if ($data !== null) {
            $body['data'] = $data;
        }

        return response()->json($body, $code);
    }

    protected function error(string $message, int $code, array $extra = []): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $message] + $extra, $code);
    }

    protected function check(Request $request, array $rules, array $messages = []): array
    {
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new HttpResponseException(
                $this->error($validator->errors()->first(), 422, ['errors' => $validator->errors()])
            );
        }

        return $validator->validated();
    }

    protected function userData(User $user): array
    {
        $user->loadMissing('department.institution');
        $department = $user->department;

        return [
            'user_id' => $user->user_id,
            'name' => $user->name,
            'npm' => $user->npm,
            'email' => $user->email,
            'role' => $user->role,
            'institution' => $department?->institution?->name,
            'faculty' => $department?->faculty,
            'department' => $department?->name,
            'department_id' => $user->department_id,
            'total_points' => $user->total_points,
            'photo_url' => $user->photo_url,
        ];
    }

    protected function articleData(Article $article, bool $withContent = false): array
    {
        $article->loadMissing('category');

        $data = [
            'article_id' => $article->article_id,
            'title' => $article->title,
            'description' => $article->description,
            'author' => $article->author,
            'url' => $article->url,
            'thumbnail_url' => $article->thumbnail_url,
            'published_at' => $article->published_at,
            'reading_time' => $article->reading_time,
            'category' => $article->category ? [
                'category_id' => $article->category->category_id,
                'name' => $article->category->name,
            ] : null,
        ];

        if ($withContent) {
            $data['content'] = $article->content;
        }

        return $data;
    }

    protected function challengeData(Challenge $challenge, ?string $todayStatus = null): array
    {
        return [
            'challenge_id' => $challenge->challenge_id,
            'title' => $challenge->title,
            'description' => $challenge->description,
            'points' => $challenge->points,
            'start_date' => $challenge->start_date,
            'end_date' => $challenge->end_date,
            'status' => $challenge->status,
            'today_status' => $todayStatus,
        ];
    }

    protected function leaderboardData(string $board): array
    {
        return LeaderboardService::get($board)->values()->map(
            fn ($row, int $index) => [
                'rank' => $index + 1,
                'name' => $row->name,
                'subtitle' => $row->sub,
                'points' => (int) $row->pts,
            ]
        )->all();
    }

    protected function paginationData($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }
}