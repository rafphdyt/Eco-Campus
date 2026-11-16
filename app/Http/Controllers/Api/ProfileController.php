<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends ApiController
{
    public function show(Request $request)
    {
        return $this->success('Data profil berhasil diambil', $this->userData($request->user()));
    }

    public function update(Request $request)
    {
        $data = $this->check($request, [
            'name' => ['required', 'string', 'max:255'],
            'npm' => ['required', 'string', 'max:30'],
            'department_id' => ['required', 'exists:departments,department_id'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'Nama wajib diisi',
            'npm.required' => 'NPM wajib diisi',
            'department_id.required' => 'Jurusan wajib dipilih',
            'department_id.exists' => 'Jurusan tidak ditemukan',
            'photo.image' => 'Foto harus berupa gambar',
            'photo.max' => 'Ukuran foto maksimal 2 MB',
        ]);

        $user = $request->user();
        $department = Department::findOrFail($data['department_id']);
        $data['institution_id'] = $department->institution_id;
        unset($data['photo']);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('avatars', 'public');
        }

        $user->update($data);

        return $this->success('Profil disimpan', $this->userData($user->fresh()));
    }

    public function destroy(Request $request)
    {
        $data = $this->check($request, ['password' => ['required']], [
            'password.required' => 'Kata sandi wajib diisi untuk menghapus akun',
        ]);

        $user = $request->user();

        if ($user->role === 'admin') {
            return $this->error('Akun admin tidak dapat dihapus', 403);
        }

        if (! Hash::check($data['password'], $user->password)) {
            return $this->error('Kata sandi salah', 422);
        }

        $id = $user->user_id;
        $user->deleteWithData();

        return $this->success('Akun berhasil dihapus', ['user_id' => $id]);
    }
}