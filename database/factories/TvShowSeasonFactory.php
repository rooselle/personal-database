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

namespace Database\Factories;

use App\Models\TvShow;
use App\Models\TvShowSeason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TvShowSeason>
 */
class TvShowSeasonFactory extends Factory
{
    public function definition(): array
    {
        $episodeCount = fake()->numberBetween(6, 13);
        $rating = fake()->optional(0.7)->numberBetween(1, 5);
        $watchedEpisodes = $rating ? $episodeCount : fake()->numberBetween(0, $episodeCount);

        return [
            'tv_show_id' => TvShow::factory(),
            'season_number' => 1,
            'episode_count' => $episodeCount,
            'watched_episodes' => $watchedEpisodes,
            'rating' => $rating,
            'is_favorite' => $rating === 5 && fake()->boolean(30),
            'comment' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }

    public function notStarted(): static
    {
        return $this->state([
            'watched_episodes' => 0,
            'rating' => null,
            'is_favorite' => false,
            'comment' => null,
        ]);
    }
}
