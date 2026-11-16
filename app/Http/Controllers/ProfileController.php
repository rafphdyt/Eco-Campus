<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('account', ['departments' => Department::all()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npm' => ['required', 'string', 'max:30'],
            'department_id' => ['required', 'exists:departments,department_id'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);
        unset($data['photo']);

        $user = $request->user();

        if ($request->hasFile('photo')) {
            if ($user->photo) Storage::disk('public')->delete($user->photo);
            $data['photo'] = $request->file('photo')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profil disimpan.');
    }

    public function destroy(Request $request)
    {
        $request->validate(
            ['password' => ['required', 'current_password']],
            [
                'password.required' => 'Kata sandi wajib diisi untuk menghapus akun.',
                'password.current_password' => 'Kata sandi salah.',
            ]
        );

        $user = $request->user();
        abort_if($user->role === 'admin', 403);

        $user->deleteWithData();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Akun berhasil dihapus.');
    }
}