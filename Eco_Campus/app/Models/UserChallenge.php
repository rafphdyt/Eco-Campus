<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserChallenge extends Model
{
    protected $fillable = ['user_id', 'challenge_id', 'proof_url', 'submitted_at',
                           'status', 'approved_at', 'points'];
    protected $casts = ['submitted_at' => 'datetime', 'approved_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function challenge() { return $this->belongsTo(Challenge::class, 'challenge_id'); }
}