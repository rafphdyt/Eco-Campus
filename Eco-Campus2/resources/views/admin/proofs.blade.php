@extends('layouts.admin')
@section('title', 'Bukti')

@section('content')
    <div class="card wide">
        <h2>Bukti yang masuk</h2>
        @forelse ($proofs as $p)
            <div class="row">
                <a href="{{ asset('storage/' . $p->proof_url) }}" target="_blank">
                    <img class="thumb" src="{{ asset('storage/' . $p->proof_url) }}" alt="Bukti">
                </a>
                <div class="c-info">
                    <strong>{{ $p->user->name }}</strong>
                    <small>{{ $p->challenge?->title ?? 'Tantangan dihapus' }} · {{ $p->points }} poin · {{ $p->submitted_at->format('d M Y H:i') }}</small>
                </div>

                @if ($p->status === 'pending')
                    <form method="POST" action="{{ route('admin.proofs.approve', $p) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button class="btn sm">Setujui</button>
                    </form>
                    <form method="POST" action="{{ route('admin.proofs.reject', $p) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button class="btn danger sm">Tolak</button>
                    </form>
                @elseif ($p->status === 'approved')
                    <span class="badge">Disetujui</span>
                    <form method="POST" action="{{ route('admin.proofs.reject', $p) }}" class="inline"
                          onsubmit="return confirm('Batalkan persetujuan? Poin akan dikurangi.')">
                        @csrf
                        @method('PATCH')
                        <button class="btn danger sm">Batalkan</button>
                    </form>
                @else
                    <span class="badge bad">Ditolak</span>
                @endif
            </div>
        @empty
            <p class="empty">Belum ada bukti masuk.</p>
        @endforelse
    </div>
@endsection