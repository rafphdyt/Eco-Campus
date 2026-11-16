<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Challenge;
use Illuminate\Http\Request;

class ChallengeController extends ApiController
{
    public function index(Request $request)
    {
        $filters = $this->check($request, [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);
        $challenges = Challenge::query()
            ->latest('challenge_id')
            ->paginate((int) ($filters['per_page'] ?? 20));

        return $this->success('Daftar tantangan admin berhasil diambil', [
            'challenges' => $challenges->getCollection()
                ->map(fn (Challenge $challenge) => $this->challengeData($challenge))
                ->values(),
            'pagination' => $this->paginationData($challenges),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->check($request, [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'points' => ['required', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);
        $challenge = Challenge::create($data + ['status' => 'active']);

        return $this->success('Tantangan ditambahkan', $this->challengeData($challenge), 201);
    }

    public function update(Request $request, Challenge $challenge)
    {
        $data = $this->check($request, [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'points' => ['sometimes', 'required', 'integer', 'min:1'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['sometimes', 'required', 'in:active,inactive'],
        ]);

        $challenge->update($data);

        return $this->success('Tantangan diperbarui', $this->challengeData($challenge->fresh()));
    }

    public function toggle(Challenge $challenge)
    {
        $challenge->update([
            'status' => $challenge->status === 'active' ? 'inactive' : 'active',
        ]);

        return $this->success('Status tantangan diperbarui', $this->challengeData($challenge->fresh()));
    }

    public function destroy(Challenge $challenge)
    {
        $id = $challenge->challenge_id;
        $challenge->delete();

        return $this->success('Tantangan dihapus', ['challenge_id' => $id]);
    }
}