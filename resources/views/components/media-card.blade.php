@props([
    'title',
    'coverUrl' => null,
    'meta' => null,
    'favorite' => false,
    'placeholderIcon' => 'book-open',
])

<div {{ $attributes->class(['group relative self-start']) }}>
    <div class="absolute inset-0 rounded-lg bg-ink-900 shadow-cutout-sm dark:bg-paper-300" aria-hidden="true"></div>

    <div class="relative flex flex-col overflow-hidden rounded-lg border-thick border-hairline bg-paper-0 transition-transform duration-normal ease-cocoon group-hover:-translate-x-0.5 group-hover:-translate-y-0.5">
        <div class="relative aspect-[2/3] overflow-hidden bg-paper-200">
            @if ($coverUrl)
                <img src="{{ $coverUrl }}" alt="{{ $title }}" class="h-full w-full object-cover" />
            @else
                <div class="flex h-full w-full items-center justify-center text-ink-300">
                    <flux:icon :name="$placeholderIcon" class="size-10" />
                </div>
            @endif

            @if ($favorite)
                <span class="absolute right-2 top-2 text-lg text-rose-500 drop-shadow">♥</span>
            @endif

            {{ $badge ?? '' }}

            @isset($delete)
                <div class="absolute left-2 top-2 opacity-0 transition-opacity duration-fast group-hover:opacity-100">
                    {{ $delete }}
                </div>
            @endisset
        </div>

        <div class="flex flex-1 flex-col gap-0.5 p-2.5">
            <p class="line-clamp-2 font-heading text-sm font-bold leading-tight text-ink-900">
                {{ $title }}
            </p>

            @if ($meta)
                <p class="text-xs text-ink-500">{{ $meta }}</p>
            @endif

            @isset($footer)
                <div class="mt-auto pt-1">{{ $footer }}</div>
            @endisset
        </div>
    </div>
</div>
