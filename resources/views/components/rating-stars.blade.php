@props([
    'value' => 0,
    'max' => 5,
    'favorite' => false,
    'size' => 'text-sm',
])

<span {{ $attributes->class(['inline-flex items-center gap-1.5']) }}>
    <span class="inline-flex gap-0.5 {{ $size }}">
        @for ($i = 1; $i <= $max; $i++)
            <span class="{{ $i <= $value ? 'text-rating-filled' : 'text-rating-empty' }}">★</span>
        @endfor
    </span>

    @if ($favorite)
        <span class="{{ $size }} text-rose-500">♥</span>
    @endif
</span>
