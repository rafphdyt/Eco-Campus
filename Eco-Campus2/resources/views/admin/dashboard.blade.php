@extends('layouts.admin')
@section('title', 'Ringkasan')

@section('content')
    <div class="card wide">
        <h2>Ringkasan</h2>
        <div class="stats">
            <div class="stat"><b>{{ $totalStudents }}</b><span>Mahasiswa</span></div>
            <div class="stat"><b>{{ $totalProofs }}</b><span>Bukti diterima</span></div>
            <div class="stat"><b>{{ $totalPoints }}</b><span>Total poin</span></div>
            <div class="stat"><b>{{ $activeChallenges }}</b><span>Tantangan aktif</span></div>
        </div>
    </div>

    <div class="card wide">
        <h2>Leaderboard</h2>
        @include('partials.leaderboard', ['routeName' => 'admin.dashboard'])
    </div>
@endsection