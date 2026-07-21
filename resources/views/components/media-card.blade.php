{{--
    "Personal Database" allow a user to register the books they've read
    and the TV shows and films they've watched, to rate them and to search
    among them.
    Copyright (C) 2026 roselle (chloe@roselle.co)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
--}}

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
