<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Movie;
use App\Models\TvShow;
use App\Models\TvShowSeason;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Book::factory(20)->create();

        Movie::factory(20)->create();

        $shows = [
            ['title' => 'The Bear', 'creators' => ['Christopher Storer'], 'genres' => ['Drama', 'Comedy'], 'year_released' => 2022, 'is_finished' => false, 'seasons' => [
                ['season_number' => 1, 'episode_count' => 8, 'watched_episodes' => 8, 'rating' => 5, 'is_favorite' => true, 'comment' => 'Masterpiece. The best season of TV in years.'],
                ['season_number' => 2, 'episode_count' => 10, 'watched_episodes' => 10, 'rating' => 5, 'is_favorite' => true, 'comment' => null],
                ['season_number' => 3, 'episode_count' => 10, 'watched_episodes' => 6, 'rating' => null, 'is_favorite' => false, 'comment' => null],
            ]],
            ['title' => 'Succession', 'creators' => ['Jesse Armstrong'], 'genres' => ['Drama', 'Comedy'], 'year_released' => 2018, 'is_finished' => true, 'seasons' => [
                ['season_number' => 1, 'episode_count' => 10, 'watched_episodes' => 10, 'rating' => 4, 'is_favorite' => false, 'comment' => null],
                ['season_number' => 2, 'episode_count' => 10, 'watched_episodes' => 10, 'rating' => 5, 'is_favorite' => false, 'comment' => null],
                ['season_number' => 3, 'episode_count' => 9, 'watched_episodes' => 9, 'rating' => 4, 'is_favorite' => false, 'comment' => null],
                ['season_number' => 4, 'episode_count' => 10, 'watched_episodes' => 10, 'rating' => 5, 'is_favorite' => true, 'comment' => 'Perfect ending.'],
            ]],
            ['title' => 'Fleabag', 'creators' => ['Phoebe Waller-Bridge'], 'genres' => ['Comedy', 'Drama'], 'year_released' => 2016, 'is_finished' => true, 'seasons' => [
                ['season_number' => 1, 'episode_count' => 6, 'watched_episodes' => 6, 'rating' => 5, 'is_favorite' => false, 'comment' => null],
                ['season_number' => 2, 'episode_count' => 6, 'watched_episodes' => 6, 'rating' => 5, 'is_favorite' => true, 'comment' => 'One of the best things ever made.'],
            ]],
            ['title' => 'Severance', 'creators' => ['Dan Erickson'], 'genres' => ['Science Fiction', 'Thriller', 'Drama'], 'year_released' => 2022, 'is_finished' => false, 'seasons' => [
                ['season_number' => 1, 'episode_count' => 9, 'watched_episodes' => 9, 'rating' => 5, 'is_favorite' => true, 'comment' => null],
                ['season_number' => 2, 'episode_count' => 10, 'watched_episodes' => 10, 'rating' => 4, 'is_favorite' => false, 'comment' => null],
            ]],
            ['title' => 'The White Lotus', 'creators' => ['Mike White'], 'genres' => ['Drama', 'Comedy', 'Mystery'], 'year_released' => 2021, 'is_finished' => false, 'seasons' => [
                ['season_number' => 1, 'episode_count' => 6, 'watched_episodes' => 6, 'rating' => 4, 'is_favorite' => false, 'comment' => null],
                ['season_number' => 2, 'episode_count' => 7, 'watched_episodes' => 7, 'rating' => 5, 'is_favorite' => true, 'comment' => 'Absolutely hooked.'],
                ['season_number' => 3, 'episode_count' => 8, 'watched_episodes' => 3, 'rating' => null, 'is_favorite' => false, 'comment' => null],
            ]],
        ];

        foreach ($shows as $showData) {
            $seasons = $showData['seasons'];
            unset($showData['seasons']);

            $show = TvShow::create($showData);

            foreach ($seasons as $seasonData) {
                TvShowSeason::create(array_merge($seasonData, ['tv_show_id' => $show->id]));
            }
        }
    }
}
