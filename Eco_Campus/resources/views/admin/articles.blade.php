@extends('layouts.admin')
@section('title', 'Artikel')

@section('content')
    <div class="card">
        <h2>Tambah artikel / berita</h2>
        <form method="POST" action="{{ route('admin.articles.store') }}">
            @csrf
            <label>Judul <input name="title" value="{{ old('title') }}" required></label>
            <label>Deskripsi singkat <input name="description" value="{{ old('description') }}"></label>
            <label>Kategori
                <select name="category_id">
                    @foreach ($categories as $c)
                        <option value="{{ $c->category_id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Penulis / sumber <input name="author" value="{{ old('author') }}" required></label>
            <label>Link <input type="url" name="url" placeholder="https://" value="{{ old('url') }}" required></label>
            <button class="btn">Tambah</button>
        </form>
    </div>

    <div class="card">
        <h2>Daftar artikel</h2>
        @forelse ($articles as $a)
            <div class="row">
                <div class="c-info">
                    <strong>{{ $a->title }}</strong>
                    <small>{{ $a->category->name }} · {{ parse_url($a->url, PHP_URL_HOST) }}</small>
                </div>
                <form method="POST" action="{{ route('admin.articles.destroy', $a) }}" class="inline"
                      onsubmit="return confirm('Hapus artikel ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn danger sm">Hapus</button>
                </form>
            </div>
        @empty
            <p class="empty">Belum ada artikel.</p>
        @endforelse
    </div>
@endsection