@extends('layouts.app')
@section('title', 'E-Library')

@section('content')
    <div class="card">
        <h2>E-Library Iklim</h2>

        <div class="chips">
            <a class="chip {{ $cat === 0 ? 'on' : '' }}" href="{{ route('library') }}">Semua</a>
            @foreach ($categories as $c)
                <a class="chip {{ $cat === $c->category_id ? 'on' : '' }}"
                   href="{{ route('library', ['cat' => $c->category_id]) }}">{{ $c->name }}</a>
            @endforeach
        </div>

        @forelse ($articles as $a)
            <a class="article" href="{{ $a->url }}" target="_blank" rel="noopener">
                <strong>{{ $a->title }}</strong>
                <p>{{ $a->description }}</p>
                <small>{{ $a->category->name }} · {{ $a->author }} · {{ parse_url($a->url, PHP_URL_HOST) }} ↗</small>
            </a>
        @empty
            <p class="empty">Belum ada konten di kategori ini.</p>
        @endforelse
    </div>
@endsection