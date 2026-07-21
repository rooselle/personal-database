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
