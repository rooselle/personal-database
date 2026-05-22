<?php

namespace Database\Factories;

use App\Models\TvShow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TvShow>
 */
class TvShowFactory extends Factory
{
    private static array $shows = [
        ['title' => 'The Bear', 'creators' => ['Christopher Storer'], 'genres' => ['Drama', 'Comedy'], 'year' => 2022, 'seasons' => 3, 'finished' => false],
        ['title' => 'Succession', 'creators' => ['Jesse Armstrong'], 'genres' => ['Drama', 'Comedy'], 'year' => 2018, 'seasons' => 4, 'finished' => true],
        ['title' => 'Fleabag', 'creators' => ['Phoebe Waller-Bridge'], 'genres' => ['Comedy', 'Drama'], 'year' => 2016, 'seasons' => 2, 'finished' => true],
        ['title' => 'Severance', 'creators' => ['Dan Erickson'], 'genres' => ['Science Fiction', 'Thriller', 'Drama'], 'year' => 2022, 'seasons' => 2, 'finished' => false],
        ['title' => 'The White Lotus', 'creators' => ['Mike White'], 'genres' => ['Drama', 'Comedy', 'Mystery'], 'year' => 2021, 'seasons' => 3, 'finished' => false],
        ['title' => 'Abbott Elementary', 'creators' => ['Quinta Brunson'], 'genres' => ['Comedy'], 'year' => 2021, 'seasons' => 4, 'finished' => false],
        ['title' => 'Andor', 'creators' => ['Tony Gilroy'], 'genres' => ['Science Fiction', 'Drama'], 'year' => 2022, 'seasons' => 2, 'finished' => false],
        ['title' => 'Station Eleven', 'creators' => ['Patrick Somerville'], 'genres' => ['Drama', 'Science Fiction'], 'year' => 2021, 'seasons' => 1, 'finished' => true],
        ['title' => 'I May Destroy You', 'creators' => ['Michaela Coel'], 'genres' => ['Drama', 'Comedy'], 'year' => 2020, 'seasons' => 1, 'finished' => true],
        ['title' => 'The Last of Us', 'creators' => ['Craig Mazin', 'Neil Druckmann'], 'genres' => ['Drama', 'Horror', 'Adventure'], 'year' => 2023, 'seasons' => 2, 'finished' => false],
    ];

    public function definition(): array
    {
        $show = fake()->randomElement(self::$shows);

        return [
            'title' => $show['title'],
            'year_released' => $show['year'],
            'creators' => $show['creators'],
            'genres' => $show['genres'],
            'is_finished' => $show['finished'],
        ];
    }

    /** @param array<string, mixed> $attributes */
    public function withSeasonCount(int $count): static
    {
        return $this->state(['_season_count' => $count]);
    }
}
