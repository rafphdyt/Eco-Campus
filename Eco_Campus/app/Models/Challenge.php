<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $primaryKey = 'challenge_id';
    protected $fillable = ['title', 'description', 'points', 'start_date', 'end_date', 'status'];

    public function userChallenges() { return $this->hasMany(UserChallenge::class, 'challenge_id'); }

    // Tantangan yang aktif DAN sedang dalam rentang tanggalnya
    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
            ->where(fn ($w) => $w->whereNull('start_date')->orWhereDate('start_date', '<=', today()))
            ->where(fn ($w) => $w->whereNull('end_date')->orWhereDate('end_date', '>=', today()));
    }

    public function isAvailable(): bool
    {
        $today = today()->toDateString();

        return $this->status === 'active'
            && (! $this->start_date || substr($this->start_date, 0, 10) <= $today)
            && (! $this->end_date || substr($this->end_date, 0, 10) >= $today);
    }
}