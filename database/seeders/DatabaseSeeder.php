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
