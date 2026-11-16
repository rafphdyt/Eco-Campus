@php
    $rank = 0;
    $max = $rows->max('pts');
@endphp

<div class="seg">
    @foreach (['orang' => 'Mahasiswa', 'jurusan' => 'Jurusan', 'fakultas' => 'Fakultas'] as $k => $v)
        <a href="{{ route($routeName, ['board' => $k]) }}" class="{{ $board === $k ? 'on' : '' }}">{{ $v }}</a>
    @endforeach
</div>

@if ($max == 0)
    <p class="hint" style="margin-top:10px">Belum ada poin. Upload bukti untuk jadi yang pertama!</p>
@endif

<ol class="lb">
    @foreach ($rows as $r)
        @php $n = $r->pts > 0 ? ++$rank : '–'; @endphp
        <li>
            <span class="rank {{ $n === 1 ? 'first' : '' }}">{{ $n }}</span>
            <div>
                <div class="lb-name">{{ $r->name }}@if ($r->sub) <small>· {{ $r->sub }}</small>@endif</div>
                <div class="bar"><span style="width: {{ $max ? ($r->pts / $max) * 100 : 0 }}%"></span></div>
            </div>
            <span class="lb-pts">{{ $r->pts }}</span>
        </li>
    @endforeach
</ol>