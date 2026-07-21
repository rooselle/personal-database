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
                'by_year' => Book::selectRaw("TO_CHAR(finished_at, 'YYYY') as year, COUNT(*) as count")
                    ->whereNotNull('finished_at')
                    ->groupByRaw("TO_CHAR(finished_at, 'YYYY')")
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
                'by_year' => Movie::selectRaw("TO_CHAR(finished_at, 'YYYY') as year, COUNT(*) as count")
                    ->whereNotNull('finished_at')
                    ->groupByRaw("TO_CHAR(finished_at, 'YYYY')")
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
        <flux:heading size="lg" class="mb-3 flex items-center gap-2 font-heading">
            <flux:icon.book-open class="size-5 text-coral-500" />
            {{ __('Books') }}
        </flux:heading>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-tile :label="__('This year')" :value="$this->stats['books']['this_year']" :sub="(string) now()->year" />
            <x-stat-tile :label="__('Last year')" :value="$this->stats['books']['last_year']" :sub="(string) now()->subYear()->year" />
            <x-stat-tile
                :label="__('All time')"
                :value="$this->stats['books']['total']"
                :sub="'★ '.($this->stats['books']['avg_rating'] ?: '—').' · '.$this->stats['books']['favorites'].' '.__('favourites')"
            />
            <div class="rounded-lg border-thick border-hairline bg-paper-0 p-5 shadow-cutout-sm">
                <flux:text size="sm" class="font-semibold text-ink-500">{{ __('Per year') }}</flux:text>
                @if(count($this->stats['books']['by_year']))
                    @php $maxBooks = max($this->stats['books']['by_year']); @endphp
                    <div class="mt-2 flex items-end gap-1">
                        @foreach($this->stats['books']['by_year'] as $year => $count)
                            <div class="relative flex flex-1 flex-col items-center gap-0.5">
                                <div
                                    class="w-full rounded-sm bg-coral-400 transition-colors hover:bg-coral-500"
                                    style="height: {{ $maxBooks > 0 ? round(($count / $maxBooks) * 36) : 0 }}px"
                                ></div>
                                <span class="text-[9px] text-ink-300">{{ substr($year, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-2 text-sm text-ink-300">—</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Movies section --}}
    <div>
        <flux:heading size="lg" class="mb-3 flex items-center gap-2 font-heading">
            <flux:icon.film class="size-5 text-teal-700 dark:text-teal-500" />
            {{ __('Movies') }}
        </flux:heading>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-tile :label="__('This year')" :value="$this->stats['movies']['this_year']" :sub="(string) now()->year" />
            <x-stat-tile :label="__('Last year')" :value="$this->stats['movies']['last_year']" :sub="(string) now()->subYear()->year" />
            <x-stat-tile
                :label="__('All time')"
                :value="$this->stats['movies']['total']"
                :sub="'★ '.($this->stats['movies']['avg_rating'] ?: '—').' · '.$this->stats['movies']['favorites'].' '.__('favourites')"
            />
            <div class="rounded-lg border-thick border-hairline bg-paper-0 p-5 shadow-cutout-sm">
                <flux:text size="sm" class="font-semibold text-ink-500">{{ __('Per year') }}</flux:text>
                @if(count($this->stats['movies']['by_year']))
                    @php $maxMovies = max($this->stats['movies']['by_year']); @endphp
                    <div class="mt-2 flex items-end gap-1">
                        @foreach($this->stats['movies']['by_year'] as $year => $count)
                            <div class="relative flex flex-1 flex-col items-center gap-0.5">
                                <div
                                    class="w-full rounded-sm bg-teal-500 transition-colors hover:bg-teal-700"
                                    style="height: {{ $maxMovies > 0 ? round(($count / $maxMovies) * 36) : 0 }}px"
                                ></div>
                                <span class="text-[9px] text-ink-300">{{ substr($year, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-2 text-sm text-ink-300">—</p>
                @endif
            </div>
        </div>
    </div>

    {{-- TV Shows section --}}
    <div>
        <flux:heading size="lg" class="mb-3 flex items-center gap-2 font-heading">
            <flux:icon.tv class="size-5 text-mustard-600 dark:text-mustard-500" />
            {{ __('TV Shows') }}
        </flux:heading>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-tile :label="__('Shows tracked')" :value="$this->stats['tv_shows']['total']" :sub="__('all time')" />
            <x-stat-tile :label="__('Completed')" :value="$this->stats['tv_shows']['completed']" :sub="__('shows finished')" />
            <x-stat-tile :label="__('Seasons')" :value="$this->stats['tv_shows']['seasons']" :sub="__('across all shows')" />
            <x-stat-tile :label="__('Average rating')" :value="$this->stats['tv_shows']['avg_rating'] ?: '—'" :sub="__('per season')" />
        </div>
    </div>

</div>
