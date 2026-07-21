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
    'showSeasonNumber' => false,
    'nextSeasonNumber' => 1,
    'currentRating' => '',
])

@if ($showSeasonNumber)
    <div class="grid grid-cols-3 gap-3">
        <flux:input
            wire:model="seasonNumber"
            :label="__('Season')"
            type="number"
            min="1"
            max="99"
            :placeholder="(string) $nextSeasonNumber"
            required
        />
        <flux:input
            wire:model="episodeCount"
            :label="__('Episodes')"
            type="number"
            min="1"
            placeholder="10"
            required
        />
        <flux:input
            wire:model="watchedEpisodes"
            :label="__('Watched')"
            type="number"
            min="0"
            placeholder="0"
            required
        />
    </div>
@else
    <div class="grid grid-cols-2 gap-3">
        <flux:input
            wire:model="episodeCount"
            :label="__('Episodes')"
            type="number"
            min="1"
            required
        />
        <flux:input
            wire:model="watchedEpisodes"
            :label="__('Watched')"
            type="number"
            min="0"
            required
        />
    </div>
@endif

<div class="space-y-3">
    <flux:select wire:model.live="seasonRating" :label="__('Rating')">
        <flux:select.option value="">{{ __('Not rated yet') }}</flux:select.option>
        <flux:select.option value="1">★☆☆☆☆ — {{ __('I hated it') }}</flux:select.option>
        <flux:select.option value="2">★★☆☆☆ — {{ __('I didn\'t like it') }}</flux:select.option>
        <flux:select.option value="3">★★★☆☆ — {{ __('I didn\'t like it much') }}</flux:select.option>
        <flux:select.option value="4">★★★★☆ — {{ __('I liked it') }}</flux:select.option>
        <flux:select.option value="5">★★★★★ — {{ __('I really liked it') }}</flux:select.option>
    </flux:select>

    @if ($currentRating === '5')
        <flux:checkbox wire:model="seasonIsFavorite" :label="__('♥ One of my favourite seasons')" />
    @endif
</div>

<flux:textarea
    wire:model="seasonComment"
    :label="__('Comment')"
    :placeholder="__('Any thoughts… (optional)')"
    rows="2"
/>
