<?php

/*
 * "Personal Database" allow a user to register the books they've read
 * and the TV shows and films they've watched, to rate them and to search
 * among them.
 * Copyright (C) 2026 roselle (chloe@roselle.co)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use App\Models\TvShow;
use App\Models\TvShowSeason;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $search = '';
    public string $displayMode = 'list';

    // Add show form
    public string $title = '';
    public string $coverUrl = '';
    public string $creatorsInput = '';
    public string $genresInput = '';
    public string $yearReleased = '';
    public bool $isFinished = false;

    // Season management
    public ?int $selectedShowId = null;
    public string $seasonNumber = '';
    public string $episodeCount = '';
    public string $watchedEpisodes = '0';
    public string $seasonRating = '';
    public bool $seasonIsFavorite = false;
    public string $seasonComment = '';

    public ?int $editingSeasonId = null;

    #[Computed]
    public function tvShows(): Collection
    {
        return TvShow::withCount('seasons')
            ->with('seasons')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhereJsonContains('creators', $this->search)
                    ->orWhereJsonContains('genres', $this->search);
            }))
            ->orderBy('title')
            ->get();
    }

    #[Computed]
    public function selectedShow(): ?TvShow
    {
        if (! $this->selectedShowId) {
            return null;
        }

        return TvShow::with('seasons')->find($this->selectedShowId);
    }

    public function updatedSeasonRating(): void
    {
        if ($this->seasonRating !== '5') {
            $this->seasonIsFavorite = false;
        }
    }

    public function saveShow(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'coverUrl' => ['nullable', 'string', 'max:2048'],
            'creatorsInput' => ['required', 'string', 'max:255'],
            'genresInput' => ['required', 'string', 'max:255'],
            'yearReleased' => ['required', 'integer', 'min:1900', 'max:'.(date('Y') + 2)],
            'isFinished' => ['boolean'],
        ]);

        TvShow::create([
            'title' => $this->title,
            'cover_url' => $this->coverUrl ?: null,
            'creators' => array_map('trim', explode(',', $this->creatorsInput)),
            'genres' => array_map('trim', explode(',', $this->genresInput)),
            'year_released' => (int) $this->yearReleased,
            'is_finished' => $this->isFinished,
        ]);

        $this->reset(['title', 'coverUrl', 'creatorsInput', 'genresInput', 'yearReleased']);
        $this->isFinished = false;
        unset($this->tvShows);

        Flux::modal('add-show')->close();
        Flux::toast(variant: 'success', text: __('Show added successfully.'));
    }

    public function deleteShow(int $id): void
    {
        TvShow::findOrFail($id)->delete();
        unset($this->tvShows);
        Flux::toast(variant: 'success', text: __('Show deleted.'));
    }

    public function openSeasons(int $showId): void
    {
        $this->selectedShowId = $showId;
        $this->resetSeasonForm();
        unset($this->selectedShow);
        Flux::modal('show-seasons')->show();
    }

    public function saveSeason(): void
    {
        $this->validate([
            'selectedShowId' => ['required', 'integer', 'exists:tv_shows,id'],
            'seasonNumber' => ['required', 'integer', 'min:1', 'max:99'],
            'episodeCount' => ['required', 'integer', 'min:1', 'max:999'],
            'watchedEpisodes' => ['required', 'integer', 'min:0', 'max:'.($this->episodeCount ?: 999)],
            'seasonRating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'seasonIsFavorite' => ['boolean'],
            'seasonComment' => ['nullable', 'string', 'max:1000'],
        ]);

        TvShowSeason::create([
            'tv_show_id' => $this->selectedShowId,
            'season_number' => (int) $this->seasonNumber,
            'episode_count' => (int) $this->episodeCount,
            'watched_episodes' => (int) $this->watchedEpisodes,
            'rating' => $this->seasonRating !== '' ? (int) $this->seasonRating : null,
            'is_favorite' => $this->seasonIsFavorite,
            'comment' => $this->seasonComment ?: null,
        ]);

        $this->resetSeasonForm();
        unset($this->tvShows, $this->selectedShow);

        Flux::toast(variant: 'success', text: __('Season added.'));
    }

    public function deleteSeason(int $id): void
    {
        TvShowSeason::findOrFail($id)->delete();
        unset($this->tvShows, $this->selectedShow);
        Flux::toast(variant: 'success', text: __('Season deleted.'));
    }

    public function openEditSeason(int $id): void
    {
        $season = TvShowSeason::findOrFail($id);
        $this->editingSeasonId = $id;
        $this->episodeCount = (string) $season->episode_count;
        $this->watchedEpisodes = (string) $season->watched_episodes;
        $this->seasonRating = $season->rating !== null ? (string) $season->rating : '';
        $this->seasonIsFavorite = $season->is_favorite;
        $this->seasonComment = $season->comment ?? '';
        $this->resetValidation(['episodeCount', 'watchedEpisodes', 'seasonRating', 'seasonIsFavorite', 'seasonComment']);
    }

    public function cancelEditSeason(): void
    {
        $this->editingSeasonId = null;
        $this->resetSeasonForm();
    }

    public function updateSeason(): void
    {
        $this->validate([
            'episodeCount' => ['required', 'integer', 'min:1', 'max:999'],
            'watchedEpisodes' => ['required', 'integer', 'min:0', 'max:'.($this->episodeCount ?: 999)],
            'seasonRating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'seasonIsFavorite' => ['boolean'],
            'seasonComment' => ['nullable', 'string', 'max:1000'],
        ]);

        TvShowSeason::findOrFail($this->editingSeasonId)->update([
            'episode_count' => (int) $this->episodeCount,
            'watched_episodes' => (int) $this->watchedEpisodes,
            'rating' => $this->seasonRating !== '' ? (int) $this->seasonRating : null,
            'is_favorite' => $this->seasonIsFavorite,
            'comment' => $this->seasonComment ?: null,
        ]);

        $this->editingSeasonId = null;
        $this->resetSeasonForm();
        unset($this->tvShows, $this->selectedShow);

        Flux::toast(variant: 'success', text: __('Season updated.'));
    }

    public function incrementWatchedEpisodes(int $id): void
    {
        $season = TvShowSeason::findOrFail($id);

        if ($season->watched_episodes < $season->episode_count) {
            $season->increment('watched_episodes');
            unset($this->tvShows, $this->selectedShow);
        }
    }

    private function resetSeasonForm(): void
    {
        $this->seasonNumber = '';
        $this->episodeCount = '';
        $this->watchedEpisodes = '0';
        $this->seasonRating = '';
        $this->seasonIsFavorite = false;
        $this->seasonComment = '';
        $this->resetValidation(['seasonNumber', 'episodeCount', 'watchedEpisodes', 'seasonRating', 'seasonIsFavorite', 'seasonComment']);
    }

    public function nextSeasonNumber(): int
    {
        if (! $this->selectedShow) {
            return 1;
        }

        return $this->selectedShow->seasons->max('season_number') + 1 ?? 1;
    }

    public function render(): \Illuminate\View\View
    {
        return $this->view()->title(__('TV Shows'));
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-4 p-4">

    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('TV Shows') }}</flux:heading>
            <flux:text class="mt-1">
                {{ $this->tvShows->count() }} {{ __('show') }}{{ $this->tvShows->count() !== 1 ? 's' : '' }}
                @if ($search) {{ __('matching your search') }} @endif
            </flux:text>
        </div>

        <flux:modal.trigger name="add-show">
            <x-cutout-button icon="plus">{{ __('Add show') }}</x-cutout-button>
        </flux:modal.trigger>
    </div>

    <div class="flex items-center gap-2">
        <flux:input
            wire:model.live="search"
            placeholder="{{ __('Search by title, creator or genre…') }}"
            icon="magnifying-glass"
            clearable
            class="flex-1"
        />
        <div class="flex items-center gap-1 shrink-0">
            <flux:button
                wire:click="$set('displayMode', 'list')"
                variant="{{ $displayMode === 'list' ? 'filled' : 'ghost' }}"
                icon="list-bullet"
                size="sm"
                title="{{ __('List view') }}"
            />
            <flux:button
                wire:click="$set('displayMode', 'gallery')"
                variant="{{ $displayMode === 'gallery' ? 'filled' : 'ghost' }}"
                icon="squares-2x2"
                size="sm"
                title="{{ __('Gallery view') }}"
            />
        </div>
    </div>

    @if ($displayMode === 'list')
        <x-data-table>
            <x-slot:columns>
                <flux:table.column></flux:table.column>
                <flux:table.column>{{ __('Title') }}</flux:table.column>
                <flux:table.column>{{ __('Year') }}</flux:table.column>
                <flux:table.column>{{ __('Creator(s)') }}</flux:table.column>
                <flux:table.column>{{ __('Genres') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Seasons') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </x-slot:columns>

            @forelse ($this->tvShows as $show)
                <flux:table.row :key="$show->id">
                    <flux:table.cell class="w-10 pr-0">
                        @if ($show->cover_url)
                            <img
                                src="{{ $show->cover_url }}"
                                alt="{{ $show->title }}"
                                class="h-12 w-8 rounded object-cover shadow-sm"
                            />
                        @else
                            <div class="h-12 w-8 rounded bg-paper-200 flex items-center justify-center">
                                <flux:icon.tv class="size-4 text-ink-300" />
                            </div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell variant="strong">{{ $show->title }}</flux:table.cell>
                    <flux:table.cell class="text-sm text-ink-500">
                        {{ $show->year_released }}
                    </flux:table.cell>
                    <flux:table.cell class="text-sm">
                        {{ implode(', ', $show->creators) }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($show->genres as $genre)
                                <x-badge size="sm">{{ $genre }}</x-badge>
                            @endforeach
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($show->is_finished)
                            <x-badge color="moss">{{ __('Finished') }}</x-badge>
                        @else
                            <x-badge color="mustard">{{ __('Ongoing') }}</x-badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($show->seasons_count > 0)
                            <button
                                wire:click="openSeasons({{ $show->id }})"
                                class="text-sm text-ink-700 hover:text-coral-500 underline underline-offset-2 transition-colors"
                            >
                                {{ $show->seasons_count }} {{ $show->seasons_count === 1 ? __('season') : __('seasons') }}
                            </button>
                        @else
                            <button
                                wire:click="openSeasons({{ $show->id }})"
                                class="text-sm text-ink-300 hover:text-coral-500 transition-colors"
                            >
                                {{ __('Add season') }}
                            </button>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button
                            wire:click="deleteShow({{ $show->id }})"
                            wire:confirm="{{ __('Delete this show and all its seasons?') }}"
                            variant="ghost"
                            size="sm"
                            icon="trash"
                            class="hover:text-rust-500!"
                        />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8">
                        <div class="py-12 text-center">
                            <flux:text>
                                @if ($search)
                                    {{ __('No shows found matching') }} "{{ $search }}".
                                @else
                                    {{ __('No TV shows yet. Add your first show!') }}
                                @endif
                            </flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </x-data-table>
    @else
        @if ($this->tvShows->isEmpty())
            <div class="py-16 text-center">
                <flux:text>
                    @if ($search)
                        {{ __('No shows found matching') }} "{{ $search }}".
                    @else
                        {{ __('No TV shows yet. Add your first show!') }}
                    @endif
                </flux:text>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                @foreach ($this->tvShows as $show)
                    <x-media-card
                        :title="$show->title"
                        :meta="implode(', ', $show->creators)"
                        :cover-url="$show->cover_url"
                        placeholder-icon="tv"
                    >
                        <x-slot:badge>
                            @if ($show->is_finished)
                                <span class="absolute top-2 right-2 drop-shadow">
                                    <x-badge color="moss" size="sm">{{ __('Finished') }}</x-badge>
                                </span>
                            @else
                                <span class="absolute top-2 right-2 drop-shadow">
                                    <x-badge color="mustard" size="sm">{{ __('Ongoing') }}</x-badge>
                                </span>
                            @endif
                        </x-slot:badge>

                        <x-slot:delete>
                            <button
                                wire:click="deleteShow({{ $show->id }})"
                                wire:confirm="{{ __('Delete this show and all its seasons?') }}"
                                class="flex size-6 items-center justify-center rounded-full border-thin border-hairline bg-paper-0 text-ink-500 shadow-cutout-sm hover:text-rust-500"
                            >
                                <flux:icon.trash class="size-3.5" />
                            </button>
                        </x-slot:delete>

                        <x-slot:footer>
                            <button
                                wire:click="openSeasons({{ $show->id }})"
                                class="text-left text-xs text-rose-500 transition-colors hover:text-rose-600 dark:hover:text-rose-300"
                            >
                                {{ $show->seasons_count }} {{ $show->seasons_count === 1 ? __('season') : __('seasons') }}
                            </button>
                        </x-slot:footer>
                    </x-media-card>
                @endforeach
            </div>
        @endif
    @endif

    <flux:modal name="add-show" class="md:w-[34rem] rounded-lg border-thick border-hairline bg-paper-0 shadow-modal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add a TV show') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Record a show you\'re watching or have watched.') }}</flux:text>
            </div>

            <form wire:submit="saveShow" class="space-y-4">
                <flux:input wire:model="title" :label="__('Title')" placeholder="{{ __('e.g. Succession') }}" required />

                <flux:input
                    wire:model="coverUrl"
                    :label="__('Cover image URL')"
                    :description="__('Paste a link to a poster image (optional)')"
                    placeholder="https://..."
                    type="url"
                />

                <div class="grid grid-cols-2 gap-4">
                    <flux:input
                        wire:model="yearReleased"
                        :label="__('First aired')"
                        type="number"
                        min="1900"
                        :max="date('Y') + 2"
                        placeholder="{{ date('Y') }}"
                        required
                    />
                    <div class="flex flex-col justify-end pb-1">
                        <flux:checkbox wire:model="isFinished" :label="__('Show is finished')" />
                    </div>
                </div>

                <flux:input
                    wire:model="creatorsInput"
                    :label="__('Creator(s) / Director(s)')"
                    :description="__('Separate multiple names with a comma')"
                    placeholder="{{ __('e.g. Jesse Armstrong') }}"
                    required
                />

                <flux:input
                    wire:model="genresInput"
                    :label="__('Genre(s)')"
                    :description="__('Separate multiple genres with a comma')"
                    placeholder="{{ __('e.g. Drama, Comedy') }}"
                    required
                />

                <div class="flex justify-end gap-2 pt-2">
                    <flux:modal.close>
                        <flux:button variant="ghost" class="font-bold!">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <x-cutout-button type="submit">{{ __('Add show') }}</x-cutout-button>
                </div>
            </form>
        </div>
    </flux:modal>

    <flux:modal name="show-seasons" flyout class="w-[32rem] border-thick border-hairline bg-paper-0 shadow-modal">
        @if ($this->selectedShow)
            <div class="flex h-full flex-col gap-6">

                <div class="flex gap-4">
                    @if ($this->selectedShow->cover_url)
                        <img
                            src="{{ $this->selectedShow->cover_url }}"
                            alt="{{ $this->selectedShow->title }}"
                            class="h-20 w-14 rounded-lg border-thick border-hairline object-cover shadow-cutout-sm shrink-0"
                        />
                    @endif
                    <div>
                        <flux:heading size="lg">{{ $this->selectedShow->title }}</flux:heading>
                        <flux:text class="mt-1">
                            {{ $this->selectedShow->year_released }} ·
                            {{ implode(', ', $this->selectedShow->creators) }} ·
                            @if ($this->selectedShow->is_finished)
                                {{ __('Finished') }}
                            @else
                                {{ __('Ongoing') }}
                            @endif
                        </flux:text>
                    </div>
                </div>

                @if ($this->selectedShow->seasons->isNotEmpty())
                    <div class="space-y-3">
                        <flux:heading size="sm">{{ __('Seasons') }}</flux:heading>

                        @foreach ($this->selectedShow->seasons as $season)
                            <div class="rounded-lg border-thick border-hairline bg-paper-0 p-3 shadow-cutout-sm" wire:key="season-{{ $season->id }}">
                                @if ($editingSeasonId === $season->id)
                                    <form wire:submit="updateSeason" class="space-y-3">
                                        <span class="font-heading font-medium text-sm">{{ __('Season') }} {{ $season->season_number }}</span>

                                        <x-season-form-fields :current-rating="$seasonRating" />

                                        <div class="flex gap-2">
                                            <x-cutout-button type="submit" size="sm">{{ __('Save changes') }}</x-cutout-button>
                                            <flux:button wire:click="cancelEditSeason" variant="ghost" size="sm" class="font-bold!">{{ __('Cancel') }}</flux:button>
                                        </div>
                                    </form>
                                @else
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="space-y-1 flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-heading font-medium text-sm">{{ __('Season') }} {{ $season->season_number }}</span>
                                                @if ($season->isFullyWatched())
                                                    <x-badge color="moss" size="sm">
                                                        <flux:icon.check class="size-3" />{{ __('Watched') }}
                                                    </x-badge>
                                                @elseif ($season->watched_episodes > 0)
                                                    <x-badge color="mustard" size="sm">{{ __('In progress') }}</x-badge>
                                                @else
                                                    <x-badge size="sm">{{ __('Not started') }}</x-badge>
                                                @endif
                                            </div>

                                            <div class="text-sm text-ink-500">
                                                {{ $season->watched_episodes }}/{{ $season->episode_count }} {{ __('episodes') }}
                                                @if ($season->episode_count > 0)
                                                    · {{ round(($season->watched_episodes / $season->episode_count) * 100) }}%
                                                @endif
                                            </div>

                                            @if ($season->rating)
                                                <x-rating-stars :value="$season->rating" :favorite="$season->is_favorite" size="text-sm" />
                                            @endif

                                            @if ($season->comment)
                                                <flux:text class="text-xs italic">{{ $season->comment }}</flux:text>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-1 shrink-0">
                                            @if (! $season->isFullyWatched())
                                                <flux:button
                                                    wire:click="incrementWatchedEpisodes({{ $season->id }})"
                                                    variant="ghost"
                                                    size="sm"
                                                    title="{{ __('Mark one more episode as watched') }}"
                                                >+1</flux:button>
                                            @endif
                                            <flux:button
                                                wire:click="openEditSeason({{ $season->id }})"
                                                variant="ghost"
                                                size="sm"
                                                icon="pencil-square"
                                            />
                                            <flux:button
                                                wire:click="deleteSeason({{ $season->id }})"
                                                wire:confirm="{{ __('Delete this season?') }}"
                                                variant="ghost"
                                                size="sm"
                                                icon="trash"
                                                class="hover:text-rust-500!"
                                            />
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (! $editingSeasonId)
                <div class="border-t border-thick border-hairline pt-4 space-y-4">
                    <flux:heading size="sm">{{ __('Add a season') }}</flux:heading>

                    <form wire:submit="saveSeason" class="space-y-4">
                        <x-season-form-fields
                            :show-season-number="true"
                            :next-season-number="$this->nextSeasonNumber()"
                            :current-rating="$seasonRating"
                        />

                        <x-cutout-button type="submit" class="w-full">
                            {{ __('Add season') }}
                        </x-cutout-button>
                    </form>
                </div>
                @endif

            </div>
        @endif
    </flux:modal>

</div>
