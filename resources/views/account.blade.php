@extends('layouts.app')
@section('title', 'Account')

@section('content')
@php $user = auth()->user(); @endphp
    <div class="card">
        <div class="prof">
            @include('partials.avatar', ['u' => $user, 'class' => 'big'])
            <strong>{{ $user->name }}</strong>
        </div>
        <dl class="info">
            <div><dt>Email</dt><dd>{{ $user->email }}</dd></div>
            <div><dt>NPM</dt><dd>{{ $user->npm ?: '-' }}</dd></div>
            <div><dt>Institusi</dt><dd>{{ $user->department->institution->name }}</dd></div>
            <div><dt>Jurusan</dt><dd>{{ $user->department->name }}</dd></div>
            <div><dt>Fakultas</dt><dd>{{ $user->department->faculty }}</dd></div>
            <div><dt>Total poin</dt><dd>{{ $user->total_points }}</dd></div>
        </dl>
    </div>

    <div class="card">
        <h2>Edit profil</h2>
        <form method="POST" action="{{ route('account.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label>Foto profil
                <input type="file" name="photo" accept="image/*">
            </label>
            <label>Nama
                <input name="name" value="{{ old('name', $user->name) }}" required>
            </label>
            <label>NPM
                <input name="npm" value="{{ old('npm', $user->npm) }}" required>
            </label>
            <label>Jurusan
                <select name="department_id">
                    @foreach ($departments as $d)
                        <option value="{{ $d->department_id }}" @selected(old('department_id', $user->department_id) == $d->department_id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Email
                <input value="{{ $user->email }}" disabled>
            </label>
            <small>Email tidak dapat diubah.</small>
            <label>Total poin
                <input value="{{ $user->total_points }}" disabled>
            </label>
            <small>Poin bertambah otomatis dari tantangan.</small>

            <button class="btn full">Simpan</button>
        </form>
    </div>

    <div class="card">
        <h2>Hapus akun</h2>
        <small>Akun, foto profil, dan seluruh riwayat tantangan akan dihapus permanen dan tidak bisa dikembalikan.</small>
        <form method="POST" action="{{ route('account.destroy') }}"
              onsubmit="return confirm('Yakin ingin menghapus akun? Tindakan ini tidak bisa dibatalkan.')">
            @csrf
            @method('DELETE')
            <label>Konfirmasi kata sandi
                <input type="password" name="password" required>
            </label>
            <button class="btn danger full">Hapus Akun</button>
        </form>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn danger full">Keluar</button>
    </form>
@endsection