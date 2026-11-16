<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $primaryKey = 'challenge_id';
    protected $fillable = ['title', 'description', 'points', 'start_date', 'end_date', 'status'];

    public function userChallenges() { return $this->hasMany(UserChallenge::class, 'challenge_id'); }
}