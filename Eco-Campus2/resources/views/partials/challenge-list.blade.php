@php $pendingIds = auth()->user()->pendingTodayIds(); @endphp

@if ($challenges->isEmpty())
    <p class="empty">Belum ada tantangan aktif.</p>
@else
    <ul class="list">
        @foreach ($challenges as $c)
            @php
                $done = in_array($c->challenge_id, $doneIds);
                $pending = in_array($c->challenge_id, $pendingIds);
            @endphp
            <li class="challenge {{ $done ? 'done' : '' }}">
                <span class="star">★</span>
                <div class="c-info">
                    <strong>{{ $c->title }}</strong>
                    <small>{{ $c->points }} poin</small>
                </div>

                @if ($done)
                    <span class="badge">Selesai</span>
                @elseif ($pending)
                    <span class="badge pending">Menunggu verifikasi</span>
                @else
                    <form method="POST" action="{{ route('challenges.upload', $c) }}"
                          enctype="multipart/form-data" class="inline">
                        @csrf
                        <input type="file" name="proof" id="proof{{ $c->challenge_id }}"
                               accept="image/*" hidden onchange="this.form.submit()">
                        <label for="proof{{ $c->challenge_id }}" class="btn">Upload bukti</label>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>
@endif