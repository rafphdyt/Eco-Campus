<?php

use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ChallengeController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Api\Admin\ChallengeController as AdminChallengeController;
use App\Http\Controllers\Api\Admin\ProofController as AdminProofController;
use Illuminate\Support\Facades\Route;

// Endpoint publik
Route::get('/institutions', [CatalogController::class, 'institutions']);
Route::get('/departments', [CatalogController::class, 'departments']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// Endpoint pengguna. Kirim header: Authorization: Bearer <token>
Route::middleware('api.auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/account', [ProfileController::class, 'show']);
    Route::match(['put', 'post'], '/account', [ProfileController::class, 'update']);
    Route::delete('/account', [ProfileController::class, 'destroy']);

    Route::get('/dashboard', [DashboardController::class, 'show']);
    Route::get('/library', [LibraryController::class, 'index']);
    Route::get('/library/articles/{article}', [LibraryController::class, 'show']);
    Route::get('/challenges', [ChallengeController::class, 'index']);
    Route::post('/challenges/{challenge}/submissions', [ChallengeController::class, 'submit']);
    Route::get('/challenges/history', [ChallengeController::class, 'history']);
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);

    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'show']);

        Route::get('/challenges', [AdminChallengeController::class, 'index']);
        Route::post('/challenges', [AdminChallengeController::class, 'store']);
        Route::match(['put', 'patch'], '/challenges/{challenge}', [AdminChallengeController::class, 'update']);
        Route::patch('/challenges/{challenge}/toggle', [AdminChallengeController::class, 'toggle']);
        Route::delete('/challenges/{challenge}', [AdminChallengeController::class, 'destroy']);

        Route::get('/categories', [AdminArticleController::class, 'categories']);
        Route::get('/articles', [AdminArticleController::class, 'index']);
        Route::post('/articles', [AdminArticleController::class, 'store']);
        Route::match(['put', 'patch'], '/articles/{article}', [AdminArticleController::class, 'update']);
        Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy']);

        Route::get('/proofs', [AdminProofController::class, 'index']);
        Route::patch('/proofs/{proof}/approve', [AdminProofController::class, 'approve']);
        Route::patch('/proofs/{proof}/reject', [AdminProofController::class, 'reject']);
    });
});