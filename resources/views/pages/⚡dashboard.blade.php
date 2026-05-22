<?php

use App\Models\Book;
use App\Models\Movie;
use App\Models\TvShow;
use App\Models\TvShowSeason;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dashboard')] class extends Component
{
    #[Computed]
    public function stats(): array
    {
        $currentYear = now()->year;
        $lastYear = now()->subYear()->year;

        return [
            'books' => [
                'total' => Book::count(),
                'this_year' => Book::whereYear('finished_at', $currentYear)->count(),
                'last_year' => Book::whereYear('finished_at', $lastYear)->count(),
                'favorites' => Book::where('is_favorite', true)->count(),
                'avg_rating' => round(Book::whereNotNull('rating')->avg('rating'), 1),
                'by_year' => Book::selectRaw('strftime("%Y", finished_at) as year, COUNT(*) as count')
                    ->whereNotNull('finished_at')
                    ->groupByRaw('strftime("%Y", finished_at)')
                    ->orderBy('year')
                    ->pluck('count', 'year')
                    ->toArray(),
            ],
            'movies' => [
                'total' => Movie::count(),
                'this_year' => Movie::whereYear('finished_at', $currentYear)->count(),
                'last_year' => Movie::whereYear('finished_at', $lastYear)->count(),
                'favorites' => Movie::where('is_favorite', true)->count(),
                'avg_rating' => round(Movie::whereNotNull('rating')->avg('rating'), 1),
                'by_year' => Movie::selectRaw('strftime("%Y", finished_at) as year, COUNT(*) as count')
                    ->whereNotNull('finished_at')
                    ->groupByRaw('strftime("%Y", finished_at)')
                    ->orderBy('year')
                    ->pluck('count', 'year')
                    ->toArray(),
            ],
            'tv_shows' => [
                'total' => TvShow::count(),
                'completed' => TvShow::where('is_finished', true)->count(),
                'seasons' => TvShowSeason::count(),
                'avg_rating' => round(TvShowSeason::whereNotNull('rating')->avg('rating'), 1),
            ],
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

    {{-- Books section --}}
    <div>
        <flux:heading size="lg" class="mb-3 flex items-center gap-2">
            <flux:icon.book-open class="size-5 text-accent" />
            {{ __('Books') }}
        </flux:heading>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-neutral-500 dark:text-zinc-400">{{ __('This year') }}</flux:text>
                <p class="mt-1 text-3xl font-bold text-neutral-900 dark:text-zinc-100">{{ $this->stats['books']['this_year'] }}</p>
                <flux:text size="xs" class="mt-1 text-neutral-400 dark:text-zinc-500">{{ now()->year }}</flux:text>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-neutral-500 dark:text-zinc-400">{{ __('Last year') }}</flux:text>
                <p class="mt-1 text-3xl font-bold text-neutral-900 dark:text-zinc-100">{{ $this->stats['books']['last_year'] }}</p>
                <flux:text size="xs" class="mt-1 text-neutral-400 dark:text-zinc-500">{{ now()->subYear()->year }}</flux:text>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-neutral-500 dark:text-zinc-400">{{ __('All time') }}</flux:text>
                <p class="mt-1 text-3xl font-bold text-neutral-900 dark:text-zinc-100">{{ $this->stats['books']['total'] }}</p>
                <flux:text size="xs" class="mt-1 text-neutral-400 dark:text-zinc-500">
                    ★ {{ $this->stats['books']['avg_rating'] ?: '—' }} &nbsp;·&nbsp; {{ $this->stats['books']['favorites'] }} {{ __('favourites') }}
                </flux:text>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-neutral-500 dark:text-zinc-400">{{ __('Per year') }}</flux:text>
                @if(count($this->stats['books']['by_year']))
                    @php $maxBooks = max($this->stats['books']['by_year']); @endphp
                    <div class="mt-2 flex h-12 items-end gap-1">
                        @foreach($this->stats['books']['by_year'] as $year => $count)
                            <div class="group relative flex flex-1 flex-col items-center">
                                <div
                                    class="w-full rounded-sm bg-accent/70 transition-colors hover:bg-accent"
                                    style="height: {{ $maxBooks > 0 ? round(($count / $maxBooks) * 100) : 0 }}%"
                                ></div>
                                <span class="mt-1 text-[9px] text-neutral-400 dark:text-zinc-500">{{ substr($year, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-2 text-sm text-neutral-400">—</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Movies section --}}
    <div>
        <flux:heading size="lg" class="mb-3 flex items-center gap-2">
            <flux:icon.film class="size-5 text-accent" />
            {{ __('Movies') }}
        </flux:heading>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-neutral-500 dark:text-zinc-400">{{ __('This year') }}</flux:text>
                <p class="mt-1 text-3xl font-bold text-neutral-900 dark:text-zinc-100">{{ $this->stats['movies']['this_year'] }}</p>
                <flux:text size="xs" class="mt-1 text-neutral-400 dark:text-zinc-500">{{ now()->year }}</flux:text>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-neutral-500 dark:text-zinc-400">{{ __('Last year') }}</flux:text>
                <p class="mt-1 text-3xl font-bold text-neutral-900 dark:text-zinc-100">{{ $this->stats['movies']['last_year'] }}</p>
                <flux:text size="xs" class="mt-1 text-neutral-400 dark:text-zinc-500">{{ now()->subYear()->year }}</flux:text>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-neutral-500 dark:text-zinc-400">{{ __('All time') }}</flux:text>
                <p class="mt-1 text-3xl font-bold text-neutral-900 dark:text-zinc-100">{{ $this->stats['movies']['total'] }}</p>
                <flux:text size="xs" class="mt-1 text-neutral-400 dark:text-zinc-500">
                    ★ {{ $this->stats['movies']['avg_rating'] ?: '—' }} &nbsp;·&nbsp; {{ $this->stats['movies']['favorites'] }} {{ __('favourites') }}
                </flux:text>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-neutral-500 dark:text-zinc-400">{{ __('Per year') }}</flux:text>
                @if(count($this->stats['movies']['by_year']))
                    @php $maxMovies = max($this->stats['movies']['by_year']); @endphp
                    <div class="mt-2 flex h-12 items-end gap-1">
                        @foreach($this->stats['movies']['by_year'] as $year => $count)
                            <div class="group relative flex flex-1 flex-col items-center">
                                <div
                                    class="w-full rounded-sm bg-accent/70 transition-colors hover:bg-accent"
                                    style="height: {{ $maxMovies > 0 ? round(($count / $maxMovies) * 100) : 0 }}%"
                                ></div>
                                <span class="mt-1 text-[9px] text-neutral-400 dark:text-zinc-500">{{ substr($year, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-2 text-sm text-neutral-400">—</p>
                @endif
            </div>
        </div>
    </div>

    {{-- TV Shows section --}}
    <div>
        <flux:heading size="lg" class="mb-3 flex items-center gap-2">
            <flux:icon.tv class="size-5 text-accent" />
            {{ __('TV Shows') }}
        </flux:heading>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-neutral-500 dark:text-zinc-400">{{ __('Shows tracked') }}</flux:text>
                <p class="mt-1 text-3xl font-bold text-neutral-900 dark:text-zinc-100">{{ $this->stats['tv_shows']['total'] }}</p>
                <flux:text size="xs" class="mt-1 text-neutral-400 dark:text-zinc-500">{{ __('all time') }}</flux:text>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-neutral-500 dark:text-zinc-400">{{ __('Completed') }}</flux:text>
                <p class="mt-1 text-3xl font-bold text-neutral-900 dark:text-zinc-100">{{ $this->stats['tv_shows']['completed'] }}</p>
                <flux:text size="xs" class="mt-1 text-neutral-400 dark:text-zinc-500">{{ __('shows finished') }}</flux:text>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-neutral-500 dark:text-zinc-400">{{ __('Seasons') }}</flux:text>
                <p class="mt-1 text-3xl font-bold text-neutral-900 dark:text-zinc-100">{{ $this->stats['tv_shows']['seasons'] }}</p>
                <flux:text size="xs" class="mt-1 text-neutral-400 dark:text-zinc-500">{{ __('across all shows') }}</flux:text>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text size="sm" class="text-neutral-500 dark:text-zinc-400">{{ __('Average rating') }}</flux:text>
                <p class="mt-1 text-3xl font-bold text-neutral-900 dark:text-zinc-100">{{ $this->stats['tv_shows']['avg_rating'] ?: '—' }}</p>
                <flux:text size="xs" class="mt-1 text-neutral-400 dark:text-zinc-500">{{ __('per season') }}</flux:text>
            </div>
        </div>
    </div>

</div>
