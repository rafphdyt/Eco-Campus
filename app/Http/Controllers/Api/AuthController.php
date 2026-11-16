<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends ApiController
{
    public function register(Request $request)
    {
        $data = $this->check($request, [
            'name' => ['required', 'string', 'max:255'],
            'npm' => ['required', 'string', 'max:30'],
            'department_id' => ['required', 'exists:departments,department_id'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama wajib diisi',
            'npm.required' => 'NPM wajib diisi',
            'department_id.required' => 'Jurusan wajib dipilih',
            'department_id.exists' => 'Jurusan tidak ditemukan',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Kata sandi wajib diisi',
            'password.min' => 'Kata sandi minimal 6 karakter',
        ]);

        $department = Department::findOrFail($data['department_id']);
        $user = User::create([
            'institution_id' => $department->institution_id,
            'department_id' => $department->department_id,
            'name' => $data['name'],
            'npm' => $data['npm'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'mahasiswa',
        ]);

        $response = $this->userData($user);
        $response['token'] = $this->issueToken($user);

        return $this->success('Akun dibuat. Selamat datang!', $response, 201);
    }

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

        $response = $this->userData($user);
        $response['token'] = $this->issueToken($user);

        return $this->success('Login berhasil', $response);
    }

    public function logout(Request $request)
    {
        $request->user()->forceFill(['api_token' => null])->save();

        return $this->success('Logout berhasil');
    }

    private function issueToken(User $user): string
    {
        $plain = Str::random(60);
        $user->forceFill(['api_token' => hash('sha256', $plain)])->save();

        return $plain;
    }
}
