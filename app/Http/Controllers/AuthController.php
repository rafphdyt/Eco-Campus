<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors(['email' => 'Email atau kata sandi salah.'])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register', ['departments' => Department::all()]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npm' => ['required', 'string', 'max:30'],
            'department_id' => ['required', 'exists:departments,department_id'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'institution_id' => Institution::first()->institution_id,
            'department_id' => $data['department_id'],
            'name' => $data['name'],
            'npm' => $data['npm'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'mahasiswa',
        ]);

        Auth::login($user);
        return redirect('/')->with('success', 'Akun dibuat. Selamat datang!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}