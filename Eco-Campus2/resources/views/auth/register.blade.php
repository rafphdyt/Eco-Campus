@extends('layouts.guest')
@section('title', 'Daftar')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <label>Nama lengkap
            <input name="name" value="{{ old('name') }}" required>
        </label>
        <label>NPM
            <input name="npm" value="{{ old('npm') }}" required>
        </label>
        <label>Jurusan
            <select name="department_id">
                @foreach ($departments as $d)
                    <option value="{{ $d->department_id }}" @selected(old('department_id') == $d->department_id)>{{ $d->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Email
            <input type="email" name="email" value="{{ old('email') }}" required>
        </label>
        <label>Kata sandi
            <input type="password" name="password" minlength="6" required>
        </label>
        <button class="btn full">Buat akun</button>
    </form>
@endsection