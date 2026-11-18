<div class="avatar {{ $class ?? '' }}">
    @if ($u->photo_url)
        <img src="{{ $u->photo_url }}" alt="">
    @else
        {{ strtoupper(mb_substr($u->name, 0, 1)) }}
    @endif
</div>