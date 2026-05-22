<?php

use App\Models\Movie;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Movies')] class extends Component
{
    public string $search = '';

    public string $title = '';
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
            'directors' => array_map('trim', explode(',', $this->directorsInput)),
            'genres' => array_map('trim', explode(',', $this->genresInput)),
            'year_released' => (int) $this->yearReleased,
            'finished_at' => $this->finishedAt,
            'rating' => (int) $this->rating,
            'is_favorite' => $this->isFavorite,
            'comment' => $this->comment ?: null,
        ]);

        $this->reset(['title', 'directorsInput', 'genresInput', 'yearReleased', 'finishedAt', 'comment']);
        $this->rating = '3';
        $this->isFavorite = false;
        unset($this->movies);

        Flux::modal('add-movie')->close();
        Flux::toast(variant: 'success', text: 'Movie added successfully.');
    }

    public function deleteMovie(int $id): void
    {
        Movie::findOrFail($id)->delete();
        unset($this->movies);
        Flux::toast(variant: 'success', text: 'Movie deleted.');
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
                <flux:button variant="primary" icon="plus">{{ __('Add movie') }}</flux:button>
            </flux:modal.trigger>
        </div>

        <flux:input
            wire:model.live="search"
            placeholder="{{ __('Search by title, director or genre…') }}"
            icon="magnifying-glass"
            clearable
        />

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Watched') }}</flux:table.column>
                <flux:table.column>{{ __('Title') }}</flux:table.column>
                <flux:table.column>{{ __('Year') }}</flux:table.column>
                <flux:table.column>{{ __('Director(s)') }}</flux:table.column>
                <flux:table.column>{{ __('Genres') }}</flux:table.column>
                <flux:table.column>{{ __('Rating') }}</flux:table.column>
                <flux:table.column>{{ __('Comment') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->movies as $movie)
                    <flux:table.row :key="$movie->id">
                        <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                            {{ $movie->finished_at->format('d/m/Y') }}
                        </flux:table.cell>
                        <flux:table.cell variant="strong">{{ $movie->title }}</flux:table.cell>
                        <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $movie->year_released }}
                        </flux:table.cell>
                        <flux:table.cell class="text-sm">
                            {{ implode(', ', $movie->directors) }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($movie->genres as $genre)
                                    <flux:badge size="sm" color="zinc">{{ $genre }}</flux:badge>
                                @endforeach
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-0.5 text-base leading-none">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $movie->rating ? 'text-amber-400' : 'text-zinc-200 dark:text-zinc-700' }}">★</span>
                                @endfor
                                @if ($movie->is_favorite)
                                    <span class="text-rose-500 ml-1">♥</span>
                                @endif
                            </div>
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
                            />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8">
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
            </flux:table.rows>
        </flux:table>

        {{-- Add Movie Modal --}}
        <flux:modal name="add-movie" class="md:w-[34rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Add a movie') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('Record a movie you\'ve watched.') }}</flux:text>
                </div>

                <form wire:submit="saveMovie" class="space-y-4">
                    <flux:input wire:model="title" :label="__('Title')" placeholder="{{ __('e.g. Parasite') }}" required />

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
                        <flux:select wire:model.live="rating" :label="__('Rating')">
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
                            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">{{ __('Add movie') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

    </div>
</div>
