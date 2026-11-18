<?php

namespace App\Http\Controllers\Api;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends ApiController
{
    // POST /api/register
    public function register(Request $request)
    {
        $data = $this->check($request, [
            'name' => ['required', 'string', 'max:255'],
            'npm' => ['required', 'string', 'max:30', 'unique:users,npm'],
            'department_id' => ['required', 'exists:departments,department_id'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama wajib diisi',
            'npm.required' => 'NPM wajib diisi',
            'npm.unique' => 'NPM sudah terdaftar',
            'department_id.required' => 'Jurusan wajib dipilih',
            'department_id.exists' => 'Jurusan tidak ditemukan',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Kata sandi wajib diisi',
            'password.min' => 'Kata sandi minimal 6 karakter',
        ]);

        $user = User::create([
            'institution_id' => Institution::first()->institution_id,
            'department_id' => $data['department_id'],
            'name' => $data['name'],
            'npm' => $data['npm'],
            'email' => $data['email'],
            'password' => $data['password'], // di-hash otomatis oleh cast 'hashed'
            'role' => 'mahasiswa',
        ]);

        $token = $this->issueToken($user);

        return $this->success('Akun dibuat. Selamat datang!', [
            'user_id' => $user->user_id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'token' => $token,
        ], 201);
    }

    // POST /api/login
    public function login(Request $request)
    {
        $data = $this->check($request, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Kata sandi wajib diisi',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return $this->error('Email atau kata sandi salah', 401);
        }

        $token = $this->issueToken($user);

        return $this->success('Login berhasil', [
            'user_id' => $user->user_id,
            'name' => $user->name,
            'email' => $user->email,
            'token' => $token,
        ]);
    }

    // POST /api/logout
    public function logout(Request $request)
    {
        $request->user()->forceFill(['api_token' => null, 'api_token_expires_at' => null])->save();
        return $this->success('Logout berhasil');
    }

    // Buat token baru (token lama otomatis tidak berlaku)
    private function issueToken(User $user): string
    {
        $plain = Str::random(60);
        $user->forceFill([
            'api_token' => hash('sha256', $plain),
            'api_token_expires_at' => now()->addDays(7),
        ])->save();

        return $plain;
    }
}