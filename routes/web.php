<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Sudah login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', fn () => auth()->user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('dashboard'));

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/library', [LibraryController::class, 'index'])->name('library');
    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges');
    Route::post('/challenges/{challenge}/upload', [ChallengeController::class, 'upload'])->name('challenges.upload');
    Route::get('/account', [ProfileController::class, 'show'])->name('account');
    Route::put('/account', [ProfileController::class, 'update'])->name('account.update');
    Route::delete('/account', [ProfileController::class, 'destroy'])->name('account.destroy');
});

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/challenges', [Admin\ChallengeController::class, 'index'])->name('challenges');
    Route::post('/challenges', [Admin\ChallengeController::class, 'store'])->name('challenges.store');
    Route::patch('/challenges/{challenge}/toggle', [Admin\ChallengeController::class, 'toggle'])->name('challenges.toggle');
    Route::delete('/challenges/{challenge}', [Admin\ChallengeController::class, 'destroy'])->name('challenges.destroy');

    Route::get('/articles', [Admin\ArticleController::class, 'index'])->name('articles');
    Route::post('/articles', [Admin\ArticleController::class, 'store'])->name('articles.store');
    Route::delete('/articles/{article}', [Admin\ArticleController::class, 'destroy'])->name('articles.destroy');

    Route::get('/proofs', [Admin\ProofController::class, 'index'])->name('proofs');
    Route::patch('/proofs/{proof}/approve', [Admin\ProofController::class, 'approve'])->name('proofs.approve');
    Route::patch('/proofs/{proof}/reject', [Admin\ProofController::class, 'reject'])->name('proofs.reject');
});