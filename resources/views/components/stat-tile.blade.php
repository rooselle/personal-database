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
