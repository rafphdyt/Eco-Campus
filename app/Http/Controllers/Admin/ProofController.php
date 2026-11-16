<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserChallenge;
use Illuminate\Support\Facades\DB;

class ProofController extends Controller
{
    public function index()
    {
        return view('admin.proofs', [
            'proofs' => UserChallenge::with(['user', 'challenge'])
                ->orderByRaw("status = 'pending' desc")
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function approve(UserChallenge $proof)
    {
        if ($proof->status !== 'pending') {
            return back()->with('error', 'Bukti ini sudah diproses.');
        }

        DB::transaction(function () use ($proof) {
            $proof->update(['status' => 'approved', 'approved_at' => now()]);
            $proof->user->increment('total_points', $proof->points);
        });

        return back()->with('success', 'Bukti disetujui, poin ditambahkan.');
    }

    public function reject(UserChallenge $proof)
    {
        if ($proof->status === 'rejected') {
            return back();
        }

        DB::transaction(function () use ($proof) {
            if ($proof->status === 'approved') {
                $user = $proof->user;
                $user->decrement('total_points', min($proof->points, $user->total_points));
            }
            $proof->update(['status' => 'rejected', 'approved_at' => null]);
        });

        return back()->with('success', 'Bukti ditolak.');
    }
}