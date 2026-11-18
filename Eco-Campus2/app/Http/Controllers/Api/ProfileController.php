<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends ApiController
{
    // GET /api/account
    public function show(Request $request)
    {
        return $this->success('Data profil berhasil diambil', $this->userData($request->user()));
    }

    // PUT /api/account  (atau POST + _method=PUT kalau upload foto)
    public function update(Request $request)
    {
        $data = $this->check($request, [
        'name' => ['sometimes', 'required', 'string', 'max:255'],
        'npm' => ['sometimes', 'required', 'string', 'max:30',
                  Rule::unique('users', 'npm')->ignore($request->user()->user_id, 'user_id')],
        'department_id' => ['sometimes', 'required', 'exists:departments,department_id'],
        'photo' => ['nullable', 'image', 'max:2048'],
    ], [
        'name.required' => 'Nama wajib diisi',
        'npm.required' => 'NPM wajib diisi',
        'npm.unique' => 'NPM sudah terdaftar',
        'department_id.required' => 'Jurusan wajib dipilih',
        'department_id.exists' => 'Jurusan tidak ditemukan',
        'photo.image' => 'Foto harus berupa gambar',
        'photo.max' => 'Ukuran foto maksimal 2 MB',
    ]);
        unset($data['photo']);

        $user = $request->user();

        if ($request->hasFile('photo')) {
            if ($user->photo) Storage::disk('public')->delete($user->photo);
            $data['photo'] = $request->file('photo')->store('avatars', 'public');
        }

        $user->update($data);

        return $this->success('Profil disimpan', $this->userData($user->fresh()));
    }

    // DELETE /api/account
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