@props([
    'label',
    'value',
    'sub' => null,
])

<div {{ $attributes->class(['rounded-lg border-thick border-hairline bg-paper-0 p-5 shadow-cutout-sm']) }}>
    <flux:text size="sm" class="font-semibold text-ink-500">{{ $label }}</flux:text>
    <div class="mt-0.5 font-heading text-3xl font-bold text-ink-900">{{ $value }}</div>
    @if ($sub)
        <flux:text size="xs" class="mt-0.5 text-ink-300">{{ $sub }}</flux:text>
    @endif
</div>
