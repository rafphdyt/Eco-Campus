<?php

use App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChallengeController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

// Otomatis berawalan /api (lihat bootstrap/app.php)

// ===== Publik =====
Route::get('/departments', [DepartmentController::class, 'index']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// ===== Harus login: header Authorization: Bearer <token> =====
Route::middleware('api.auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Akun
    Route::get('/account', [ProfileController::class, 'show']);
    Route::match(['put', 'post'], '/account', [ProfileController::class, 'update']);
    Route::delete('/account', [ProfileController::class, 'destroy']);

    // Mahasiswa
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/challenges', [ChallengeController::class, 'index']);
    Route::post('/challenges/{challenge}/upload', [ChallengeController::class, 'upload']);
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);
    Route::get('/library', [LibraryController::class, 'index']);

    // ===== Admin saja =====
    Route::prefix('admin')->middleware('api.admin')->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index']);

        Route::get('/challenges', [Admin\ChallengeController::class, 'index']);
        Route::post('/challenges', [Admin\ChallengeController::class, 'store']);
        Route::patch('/challenges/{challenge}/toggle', [Admin\ChallengeController::class, 'toggle']);
        Route::delete('/challenges/{challenge}', [Admin\ChallengeController::class, 'destroy']);

        Route::get('/articles', [Admin\ArticleController::class, 'index']);
        Route::post('/articles', [Admin\ArticleController::class, 'store']);
        Route::delete('/articles/{article}', [Admin\ArticleController::class, 'destroy']);

        Route::get('/proofs', [Admin\ProofController::class, 'index']);
        Route::patch('/proofs/{proof}/approve', [Admin\ProofController::class, 'approve']);
        Route::patch('/proofs/{proof}/reject', [Admin\ProofController::class, 'reject']);
    });
});