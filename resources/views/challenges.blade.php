@extends('layouts.app')
@section('title', 'Challenge')

@section('content')
    <div class="card">
        <h2>Tantangan</h2>
        @include('partials.challenge-list')
    </div>

    <div class="card">
        <h2>Leaderboard</h2>
        @include('partials.leaderboard', ['routeName' => 'challenges'])
    </div>
@endsection