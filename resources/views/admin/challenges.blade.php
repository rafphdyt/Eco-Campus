@extends('layouts.admin')
@section('title', 'Tantangan')

@section('content')
    <div class="card">
        <h2>Tambah tantangan</h2>
        <form method="POST" action="{{ route('admin.challenges.store') }}">
            @csrf
            <label>Judul <input name="title" value="{{ old('title') }}" required></label>
            <label>Deskripsi <input name="description" value="{{ old('description') }}"></label>
            <label>Poin <input type="number" name="points" min="1" value="{{ old('points', 10) }}" required></label>
            <button class="btn">Tambah</button>
        </form>
    </div>

    <div class="card">
        <h2>Daftar tantangan</h2>
        @forelse ($challenges as $c)
            <div class="row">
                <div class="c-info">
                    <strong>{{ $c->title }}</strong>
                    <small>{{ $c->points }} poin · {{ $c->status === 'active' ? 'Aktif' : 'Nonaktif' }}</small>
                </div>

                <form method="POST" action="{{ route('admin.challenges.toggle', $c) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button class="btn ghost sm">{{ $c->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                </form>

                <form method="POST" action="{{ route('admin.challenges.destroy', $c) }}" class="inline"
                      onsubmit="return confirm('Hapus tantangan ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn danger sm">Hapus</button>
                </form>
            </div>
        @empty
            <p class="empty">Belum ada tantangan.</p>
        @endforelse
    </div>
@endsection