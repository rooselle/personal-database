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

use App\Models\Movie;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $search = '';
    public string $displayMode = 'list';

    public string $title = '';
    public string $coverUrl = '';
    public string $directorsInput = '';
    public string $genresInput = '';
    public string $yearReleased = '';
    public string $finishedAt = '';
    public string $rating = '3';
    public bool $isFavorite = false;
    public string $comment = '';

    #[Computed]
    public function movies(): Collection
    {
        return Movie::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhereJsonContains('directors', $this->search)
                    ->orWhereJsonContains('genres', $this->search);
            }))
            ->orderBy('finished_at', 'desc')
            ->get();
    }

    public function updatedRating(): void
    {
        if ($this->rating < 5) {
            $this->isFavorite = false;
        }
    }

    public function saveMovie(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'coverUrl' => ['nullable', 'string', 'max:2048'],
            'directorsInput' => ['required', 'string', 'max:255'],
            'genresInput' => ['required', 'string', 'max:255'],
            'yearReleased' => ['required', 'integer', 'min:1888', 'max:'.date('Y')],
            'finishedAt' => ['required', 'date', 'before_or_equal:today'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'isFavorite' => ['boolean'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Movie::create([
            'title' => $this->title,
            'cover_url' => $this->coverUrl ?: null,
            'directors' => array_map('trim', explode(',', $this->directorsInput)),
            'genres' => array_map('trim', explode(',', $this->genresInput)),
            'year_released' => (int) $this->yearReleased,
            'finished_at' => $this->finishedAt,
            'rating' => (int) $this->rating,
            'is_favorite' => $this->isFavorite,
            'comment' => $this->comment ?: null,
        ]);

        $this->reset(['title', 'coverUrl', 'directorsInput', 'genresInput', 'yearReleased', 'finishedAt', 'comment']);
        $this->rating = '3';
        $this->isFavorite = false;
        unset($this->movies);

        Flux::modal('add-movie')->close();
        Flux::toast(variant: 'success', text: __('Movie added successfully.'));
    }

    public function deleteMovie(int $id): void
    {
        Movie::findOrFail($id)->delete();
        unset($this->movies);
        Flux::toast(variant: 'success', text: __('Movie deleted.'));
    }

    public function render(): \Illuminate\View\View
    {
        return $this->view()->title(__('Movies'));
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-4 p-4">

    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Movies') }}</flux:heading>
            <flux:text class="mt-1">
                {{ $this->movies->count() }} {{ __('movie') }}{{ $this->movies->count() !== 1 ? 's' : '' }}
                @if ($search) {{ __('matching your search') }} @endif
            </flux:text>
        </div>

        <flux:modal.trigger name="add-movie">
            <x-cutout-button icon="plus">{{ __('Add movie') }}</x-cutout-button>
        </flux:modal.trigger>
    </div>

    <div class="flex items-center gap-2">
        <flux:input
            wire:model.live="search"
            placeholder="{{ __('Search by title, director or genre…') }}"
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
                <flux:table.column>{{ __('Watched on') }}</flux:table.column>
                <flux:table.column>{{ __('Title') }}</flux:table.column>
                <flux:table.column>{{ __('Year') }}</flux:table.column>
                <flux:table.column>{{ __('Director(s)') }}</flux:table.column>
                <flux:table.column>{{ __('Genres') }}</flux:table.column>
                <flux:table.column>{{ __('Rating') }}</flux:table.column>
                <flux:table.column>{{ __('Comment') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </x-slot:columns>

            @forelse ($this->movies as $movie)
                <flux:table.row :key="$movie->id">
                    <flux:table.cell class="w-10 pr-0">
                        @if ($movie->cover_url)
                            <img
                                src="{{ $movie->cover_url }}"
                                alt="{{ $movie->title }}"
                                class="h-12 w-8 rounded object-cover shadow-sm"
                            />
                        @else
                            <div class="h-12 w-8 rounded bg-paper-200 flex items-center justify-center">
                                <flux:icon.film class="size-4 text-ink-300" />
                            </div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="text-sm text-ink-500 whitespace-nowrap">
                        {{ $movie->finished_at->format('d/m/Y') }}
                    </flux:table.cell>
                    <flux:table.cell variant="strong">{{ $movie->title }}</flux:table.cell>
                    <flux:table.cell class="text-sm text-ink-500">
                        {{ $movie->year_released }}
                    </flux:table.cell>
                    <flux:table.cell class="text-sm">
                        {{ implode(', ', $movie->directors) }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($movie->genres as $genre)
                                <x-badge size="sm">{{ $genre }}</x-badge>
                            @endforeach
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <x-rating-stars :value="$movie->rating" :favorite="$movie->is_favorite" size="text-base" />
                    </flux:table.cell>
                    <flux:table.cell class="max-w-xs">
                        @if ($movie->comment)
                            <flux:text class="text-sm truncate">{{ $movie->comment }}</flux:text>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button
                            wire:click="deleteMovie({{ $movie->id }})"
                            wire:confirm="{{ __('Delete this movie?') }}"
                            variant="ghost"
                            size="sm"
                            icon="trash"
                            class="hover:text-rust-500!"
                        />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="9">
                        <div class="py-12 text-center">
                            <flux:text>
                                @if ($search)
                                    {{ __('No movies found matching') }} "{{ $search }}".
                                @else
                                    {{ __('No movies yet. Add your first movie!') }}
                                @endif
                            </flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </x-data-table>
    @else
        @if ($this->movies->isEmpty())
            <div class="py-16 text-center">
                <flux:text>
                    @if ($search)
                        {{ __('No movies found matching') }} "{{ $search }}".
                    @else
                        {{ __('No movies yet. Add your first movie!') }}
                    @endif
                </flux:text>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                @foreach ($this->movies as $movie)
                    <x-media-card
                        :title="$movie->title"
                        :meta="implode(', ', $movie->directors)"
                        :cover-url="$movie->cover_url"
                        :favorite="$movie->is_favorite"
                        placeholder-icon="film"
                    >
                        <x-slot:delete>
                            <button
                                wire:click="deleteMovie({{ $movie->id }})"
                                wire:confirm="{{ __('Delete this movie?') }}"
                                class="flex size-6 items-center justify-center rounded-full border-thin border-hairline bg-paper-0 text-ink-500 shadow-cutout-sm hover:text-rust-500"
                            >
                                <flux:icon.trash class="size-3.5" />
                            </button>
                        </x-slot:delete>

                        <x-slot:footer>
                            <x-rating-stars :value="$movie->rating" size="text-xs" />
                        </x-slot:footer>
                    </x-media-card>
                @endforeach
            </div>
        @endif
    @endif

    <flux:modal name="add-movie" class="md:w-[34rem] rounded-lg border-thick border-hairline bg-paper-0 shadow-modal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add a movie') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Record a movie you\'ve watched.') }}</flux:text>
            </div>

            <form wire:submit="saveMovie" class="space-y-4">
                <flux:input wire:model="title" :label="__('Title')" placeholder="{{ __('e.g. Parasite') }}" required />

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
                        :label="__('Year released')"
                        type="number"
                        min="1888"
                        :max="date('Y')"
                        placeholder="{{ date('Y') }}"
                        required
                    />
                    <flux:input wire:model="finishedAt" :label="__('Date watched')" type="date" required />
                </div>

                <flux:input
                    wire:model="directorsInput"
                    :label="__('Director(s)')"
                    :description="__('Separate multiple names with a comma')"
                    placeholder="{{ __('e.g. Bong Joon-ho') }}"
                    required
                />

                <flux:input
                    wire:model="genresInput"
                    :label="__('Genre(s)')"
                    :description="__('Separate multiple genres with a comma')"
                    placeholder="{{ __('e.g. Drama, Thriller') }}"
                    required
                />

                <div class="space-y-3">
                    <flux:select wire:model.live="rating" :label="__('Rating')" required>
                        <flux:select.option value="1">★☆☆☆☆ — {{ __('I hated it') }}</flux:select.option>
                        <flux:select.option value="2">★★☆☆☆ — {{ __('I didn\'t like it') }}</flux:select.option>
                        <flux:select.option value="3">★★★☆☆ — {{ __('I didn\'t like it much') }}</flux:select.option>
                        <flux:select.option value="4">★★★★☆ — {{ __('I liked it') }}</flux:select.option>
                        <flux:select.option value="5">★★★★★ — {{ __('I really liked it') }}</flux:select.option>
                    </flux:select>

                    @if ($rating == '5')
                        <flux:checkbox wire:model="isFavorite" :label="__('♥ One of my favourite movies of the year')" />
                    @endif
                </div>

                <flux:textarea
                    wire:model="comment"
                    :label="__('Comment')"
                    :placeholder="__('Any thoughts… (optional)')"
                    rows="2"
                />

                <div class="flex justify-end gap-2 pt-2">
                    <flux:modal.close>
                        <flux:button variant="ghost" class="font-bold!">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <x-cutout-button type="submit">{{ __('Add movie') }}</x-cutout-button>
                </div>
            </form>
        </div>
    </flux:modal>

</div>
