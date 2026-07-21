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
