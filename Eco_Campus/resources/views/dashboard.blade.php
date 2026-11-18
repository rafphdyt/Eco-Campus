@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="card">
        <h2>Tantangan hari ini</h2>
        @include('partials.challenge-list')
    </div>

    <div class="card">
        <h2>E-Library</h2>
        @if ($articles->isEmpty())
            <p class="empty">Belum ada konten.</p>
        @else
            <div class="carousel" id="carousel">
                @foreach ($articles as $a)
                    <a class="slide" href="{{ $a->url }}" target="_blank" rel="noopener">
                        <small>{{ $a->category->name }} · {{ parse_url($a->url, PHP_URL_HOST) }} ↗</small>
                        <strong>{{ $a->title }}</strong>
                    </a>
                @endforeach
            </div>
            <div class="dots">
                @foreach ($articles as $i => $a)
                    <i class="{{ $i === 0 ? 'on' : '' }}"></i>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        const carousel = document.getElementById('carousel');
        if (carousel) {
            carousel.addEventListener('scroll', () => {
                const i = Math.round(carousel.scrollLeft / carousel.clientWidth);
                document.querySelectorAll('.dots i').forEach((d, n) => d.classList.toggle('on', n === i));
            });
        }
    </script>
@endsection