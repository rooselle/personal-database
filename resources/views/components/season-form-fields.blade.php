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
