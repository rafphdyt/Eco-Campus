<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Challenge;
use Illuminate\Http\Request;

class ChallengeController extends ApiController
{
    // GET /api/admin/challenges
    public function index()
    {
        $data = Challenge::orderByDesc('challenge_id')->get()
            ->map(fn ($c) => $c->only(['challenge_id', 'title', 'description', 'points', 'status']))->all();

        return $this->success('Data tantangan berhasil diambil', $data);
    }

    // POST /api/admin/challenges
    public function store(Request $request)
    {
        $data = $this->check($request, [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'points' => ['required', 'integer', 'min:1'],
        ], [
            'title.required' => 'Judul wajib diisi',
            'points.required' => 'Poin wajib diisi',
            'points.integer' => 'Poin harus berupa angka',
            'points.min' => 'Poin minimal 1',
            'start_date.date' => 'Format tanggal mulai tidak valid',
            'end_date.date' => 'Format tanggal selesai tidak valid',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai',
        ]);

        $c = Challenge::create($data + ['status' => 'active']);

        return $this->success('Tantangan ditambahkan',
            $c->only(['challenge_id', 'title', 'description', 'points', 'status']), 201);
    }

    // PATCH /api/admin/challenges/{challenge}/toggle
    public function toggle(Challenge $challenge)
    {
        $challenge->update(['status' => $challenge->status === 'active' ? 'inactive' : 'active']);

        return $this->success('Status tantangan diubah',
            $challenge->only(['challenge_id', 'title', 'status']));
    }

    // DELETE /api/admin/challenges/{challenge}
    public function destroy(Challenge $challenge)
    {
        $id = $challenge->challenge_id;
        $challenge->delete();

        return $this->success('Tantangan dihapus', ['challenge_id' => $id]);
    }
}