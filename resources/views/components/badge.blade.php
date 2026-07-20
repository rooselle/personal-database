@props([
    'color' => 'neutral',
    'size' => 'md',
])

@php
    $colorClasses = match ($color) {
        'coral' => 'bg-coral-100 text-coral-600 border-coral-600',
        'teal' => 'bg-teal-100 text-teal-700 border-teal-700',
        'mustard' => 'bg-mustard-100 text-mustard-600 border-mustard-600',
        'moss' => 'bg-moss-100 text-moss-600 border-moss-600',
        'rose' => 'bg-rose-100 text-rose-600 border-rose-600',
        default => 'bg-paper-200 text-ink-700 border-hairline',
    };

    $sizeClasses = $size === 'sm' ? 'text-xs px-[9px] py-0.5' : 'text-sm px-3 py-1';
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1 whitespace-nowrap rounded-full border-thin font-semibold leading-[1.6]',
    $colorClasses,
    $sizeClasses,
]) }}>{{ $slot }}</span>
