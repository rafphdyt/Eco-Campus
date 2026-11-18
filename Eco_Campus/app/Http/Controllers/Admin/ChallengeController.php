<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index()
    {
        return view('admin.challenges', ['challenges' => Challenge::orderByDesc('challenge_id')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'points' => ['required', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);
        Challenge::create($data + ['status' => 'active']);
        return back()->with('success', 'Tantangan ditambahkan.');
    }

    public function toggle(Challenge $challenge)
    {
        $challenge->update(['status' => $challenge->status === 'active' ? 'inactive' : 'active']);
        return back();
    }

    public function destroy(Challenge $challenge)
    {
        $challenge->delete();
        return back()->with('success', 'Tantangan dihapus.');
    }
}