<?php

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
