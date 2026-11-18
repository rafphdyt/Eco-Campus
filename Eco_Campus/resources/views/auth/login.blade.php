@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label>Email
            <input type="email" name="email" value="{{ old('email') }}" required>
        </label>
        <label>Kata sandi
            <input type="password" name="password" required>
        </label>
        <button class="btn full">Masuk</button>
    </form>
@endsection