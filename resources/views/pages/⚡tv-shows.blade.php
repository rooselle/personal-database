<?php

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
            <flux:button variant="primary" icon="plus">{{ __('Add show') }}</flux:button>
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
        <flux:table>
            <flux:table.columns>
                <flux:table.column></flux:table.column>
                <flux:table.column>{{ __('Title') }}</flux:table.column>
                <flux:table.column>{{ __('Year') }}</flux:table.column>
                <flux:table.column>{{ __('Creator(s)') }}</flux:table.column>
                <flux:table.column>{{ __('Genres') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Seasons') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
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
                                <div class="h-12 w-8 rounded bg-rose-100 dark:bg-zinc-700 flex items-center justify-center">
                                    <flux:icon.tv class="size-4 text-rose-300 dark:text-zinc-500" />
                                </div>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell variant="strong">{{ $show->title }}</flux:table.cell>
                        <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $show->year_released }}
                        </flux:table.cell>
                        <flux:table.cell class="text-sm">
                            {{ implode(', ', $show->creators) }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($show->genres as $genre)
                                    <flux:badge size="sm" color="zinc">{{ $genre }}</flux:badge>
                                @endforeach
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($show->is_finished)
                                <flux:badge color="zinc">{{ __('Finished') }}</flux:badge>
                            @else
                                <flux:badge color="lime">{{ __('Ongoing') }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($show->seasons_count > 0)
                                <button
                                    wire:click="openSeasons({{ $show->id }})"
                                    class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 underline underline-offset-2 transition-colors"
                                >
                                    {{ $show->seasons_count }} {{ $show->seasons_count === 1 ? __('season') : __('seasons') }}
                                </button>
                            @else
                                <button
                                    wire:click="openSeasons({{ $show->id }})"
                                    class="text-sm text-zinc-400 dark:text-zinc-500 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
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
            </flux:table.rows>
        </flux:table>
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
                    <div class="group relative flex flex-col rounded-xl overflow-hidden border border-rose-100 dark:border-zinc-700 shadow-sm hover:shadow-md transition-shadow bg-white dark:bg-zinc-800">
                        <div class="aspect-[2/3] bg-rose-50 dark:bg-zinc-700 relative overflow-hidden">
                            @if ($show->cover_url)
                                <img
                                    src="{{ $show->cover_url }}"
                                    alt="{{ $show->title }}"
                                    class="w-full h-full object-cover"
                                />
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center gap-2">
                                    <flux:icon.tv class="size-10 text-rose-200 dark:text-zinc-500" />
                                </div>
                            @endif
                            @if ($show->is_finished)
                                <span class="absolute top-2 right-2 drop-shadow">
                                    <flux:badge size="sm" color="zinc" variant="solid">{{ __('Finished') }}</flux:badge>
                                </span>
                            @else
                                <span class="absolute top-2 right-2 drop-shadow">
                                    <flux:badge size="sm" color="lime" variant="solid">{{ __('Ongoing') }}</flux:badge>
                                </span>
                            @endif
                        </div>
                        <div class="p-2.5 flex flex-col gap-1 flex-1">
                            <p class="font-semibold text-sm leading-tight line-clamp-2">{{ $show->title }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-tight">{{ implode(', ', $show->creators) }}</p>
                            <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $show->year_released }}</p>
                            <button
                                wire:click="openSeasons({{ $show->id }})"
                                class="text-xs text-left mt-auto pt-1 text-rose-500 hover:text-rose-700 dark:hover:text-rose-300 transition-colors"
                            >
                                {{ $show->seasons_count }} {{ $show->seasons_count === 1 ? __('season') : __('seasons') }}
                            </button>
                        </div>
                        <button
                            wire:click="deleteShow({{ $show->id }})"
                            wire:confirm="{{ __('Delete this show and all its seasons?') }}"
                            class="absolute top-2 left-2 size-6 rounded-full bg-white/80 dark:bg-zinc-800/80 backdrop-blur-sm flex items-center justify-center text-zinc-400 hover:text-rose-500 opacity-0 group-hover:opacity-100 transition-opacity shadow-sm"
                        >
                            <flux:icon.trash class="size-3.5" />
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    {{-- Add Show Modal --}}
    <flux:modal name="add-show" class="md:w-[34rem]">
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
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">{{ __('Add show') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Seasons Flyout --}}
    <flux:modal name="show-seasons" flyout class="w-[32rem]">
        @if ($this->selectedShow)
            <div class="flex h-full flex-col gap-6">

                {{-- Show header --}}
                <div class="flex gap-4">
                    @if ($this->selectedShow->cover_url)
                        <img
                            src="{{ $this->selectedShow->cover_url }}"
                            alt="{{ $this->selectedShow->title }}"
                            class="h-20 w-14 rounded-lg object-cover shadow-sm shrink-0"
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

                {{-- Seasons list --}}
                @if ($this->selectedShow->seasons->isNotEmpty())
                    <div class="space-y-3">
                        <flux:heading size="sm">{{ __('Seasons') }}</flux:heading>

                        @foreach ($this->selectedShow->seasons as $season)
                            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="space-y-1 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-sm">{{ __('Season') }} {{ $season->season_number }}</span>
                                            @if ($season->isFullyWatched())
                                                <flux:badge size="sm" color="lime" icon="check">{{ __('Watched') }}</flux:badge>
                                            @elseif ($season->watched_episodes > 0)
                                                <flux:badge size="sm" color="amber">{{ __('In progress') }}</flux:badge>
                                            @else
                                                <flux:badge size="sm" color="zinc">{{ __('Not started') }}</flux:badge>
                                            @endif
                                        </div>

                                        <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $season->watched_episodes }}/{{ $season->episode_count }} {{ __('episodes') }}
                                            @if ($season->episode_count > 0)
                                                · {{ round(($season->watched_episodes / $season->episode_count) * 100) }}%
                                            @endif
                                        </div>

                                        @if ($season->rating)
                                            <div class="flex items-center gap-0.5 text-sm leading-none">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span class="{{ $i <= $season->rating ? 'text-amber-400' : 'text-zinc-200 dark:text-zinc-700' }}">★</span>
                                                @endfor
                                                @if ($season->is_favorite)
                                                    <span class="text-rose-500 ml-1">♥</span>
                                                @endif
                                            </div>
                                        @endif

                                        @if ($season->comment)
                                            <flux:text class="text-xs italic">{{ $season->comment }}</flux:text>
                                        @endif
                                    </div>

                                    <flux:button
                                        wire:click="deleteSeason({{ $season->id }})"
                                        wire:confirm="{{ __('Delete this season?') }}"
                                        variant="ghost"
                                        size="sm"
                                        icon="trash"
                                    />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Add season form --}}
                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-4">
                    <flux:heading size="sm">{{ __('Add a season') }}</flux:heading>

                    <form wire:submit="saveSeason" class="space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <flux:input
                                wire:model="seasonNumber"
                                :label="__('Season')"
                                type="number"
                                min="1"
                                max="99"
                                :placeholder="(string) $this->nextSeasonNumber()"
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

                        <div class="space-y-3">
                            <flux:select wire:model.live="seasonRating" :label="__('Rating')">
                                <flux:select.option value="">{{ __('Not rated yet') }}</flux:select.option>
                                <flux:select.option value="1">★☆☆☆☆ — {{ __('I hated it') }}</flux:select.option>
                                <flux:select.option value="2">★★☆☆☆ — {{ __('I didn\'t like it') }}</flux:select.option>
                                <flux:select.option value="3">★★★☆☆ — {{ __('I didn\'t like it much') }}</flux:select.option>
                                <flux:select.option value="4">★★★★☆ — {{ __('I liked it') }}</flux:select.option>
                                <flux:select.option value="5">★★★★★ — {{ __('I really liked it') }}</flux:select.option>
                            </flux:select>

                            @if ($seasonRating === '5')
                                <flux:checkbox wire:model="seasonIsFavorite" :label="__('♥ One of my favourite seasons')" />
                            @endif
                        </div>

                        <flux:textarea
                            wire:model="seasonComment"
                            :label="__('Comment')"
                            :placeholder="__('Any thoughts… (optional)')"
                            rows="2"
                        />

                        <flux:button type="submit" variant="primary" class="w-full">
                            {{ __('Add season') }}
                        </flux:button>
                    </form>
                </div>

            </div>
        @endif
    </flux:modal>

</div>
