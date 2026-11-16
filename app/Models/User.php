<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';

    // total_points SENGAJA tidak ada di sini: poin hanya berubah lewat kode kita.
    protected $fillable = ['institution_id', 'department_id', 'name', 'npm',
                           'email', 'password', 'role', 'photo'];

    protected $hidden = ['password', 'remember_token', 'api_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function department() { return $this->belongsTo(Department::class, 'department_id'); }
    public function userChallenges() { return $this->hasMany(UserChallenge::class, 'user_id'); }

    // $user->photo_url
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    // id tantangan yang sudah diselesaikan hari ini
    public function doneTodayIds(): array
    {
        return $this->userChallenges()
            ->where('status', 'approved')
            ->whereDate('submitted_at', today())
            ->pluck('challenge_id')->all();
    }

    public function pendingTodayIds(): array
    {
        return $this->userChallenges()
            ->where('status', 'pending')
            ->whereDate('submitted_at', today())
            ->pluck('challenge_id')->all();
    }

    // Hapus akun beserta riwayat tantangan & file-nya.
    // Riwayat harus dihapus dulu karena user_challenges punya foreign key ke users.
    public function deleteWithData(): void
    {
        $files = $this->userChallenges()->pluck('proof_url')->filter()->all();
        if ($this->photo) $files[] = $this->photo;

        DB::transaction(function () {
            $this->userChallenges()->delete();
            $this->delete();
        });

        Storage::disk('public')->delete($files);
    }
}