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

        TvShow::factory(5)->create()->each(function (TvShow $show) {
            $seasonCount = fake()->numberBetween(1, 4);
            $previousCompleted = true;

            foreach (range(1, $seasonCount) as $seasonNumber) {
                $season = TvShowSeason::factory()
                    ->when(! $previousCompleted, fn ($f) => $f->notStarted())
                    ->create([
                        'tv_show_id' => $show->id,
                        'season_number' => $seasonNumber,
                    ]);

                $previousCompleted = $season->watched_episodes === $season->episode_count;
            }
        });
    }
}
