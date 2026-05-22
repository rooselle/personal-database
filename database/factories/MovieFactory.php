<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Movie>
 */
class MovieFactory extends Factory
{
    private static array $movies = [
        ['title' => 'Parasite', 'directors' => ['Bong Joon-ho'], 'genres' => ['Thriller', 'Drama'], 'year' => 2019],
        ['title' => 'Everything Everywhere All at Once', 'directors' => ['Daniel Kwan', 'Daniel Scheinert'], 'genres' => ['Science Fiction', 'Comedy', 'Drama'], 'year' => 2022],
        ['title' => 'Portrait of a Lady on Fire', 'directors' => ['Céline Sciamma'], 'genres' => ['Drama', 'Romance'], 'year' => 2019],
        ['title' => 'The Grand Budapest Hotel', 'directors' => ['Wes Anderson'], 'genres' => ['Comedy', 'Drama'], 'year' => 2014],
        ['title' => 'La La Land', 'directors' => ['Damien Chazelle'], 'genres' => ['Musical', 'Romance', 'Drama'], 'year' => 2016],
        ['title' => 'Spirited Away', 'directors' => ['Hayao Miyazaki'], 'genres' => ['Animation', 'Fantasy', 'Adventure'], 'year' => 2001],
        ['title' => 'Moonlight', 'directors' => ['Barry Jenkins'], 'genres' => ['Drama'], 'year' => 2016],
        ['title' => 'Hereditary', 'directors' => ['Ari Aster'], 'genres' => ['Horror', 'Drama'], 'year' => 2018],
        ['title' => 'The Favourite', 'directors' => ['Yorgos Lanthimos'], 'genres' => ['Drama', 'Comedy'], 'year' => 2018],
        ['title' => 'Past Lives', 'directors' => ['Celine Song'], 'genres' => ['Drama', 'Romance'], 'year' => 2023],
        ['title' => 'Aftersun', 'directors' => ['Charlotte Wells'], 'genres' => ['Drama'], 'year' => 2022],
        ['title' => 'The Whale', 'directors' => ['Darren Aronofsky'], 'genres' => ['Drama'], 'year' => 2022],
        ['title' => 'Little Women', 'directors' => ['Greta Gerwig'], 'genres' => ['Drama', 'Romance'], 'year' => 2019],
        ['title' => 'Tár', 'directors' => ['Todd Field'], 'genres' => ['Drama', 'Music'], 'year' => 2022],
        ['title' => 'The Power of the Dog', 'directors' => ['Jane Campion'], 'genres' => ['Drama', 'Western'], 'year' => 2021],
    ];

    public function definition(): array
    {
        $movie = fake()->randomElement(self::$movies);
        $rating = fake()->numberBetween(1, 5);

        return [
            'title' => $movie['title'],
            'year_released' => $movie['year'],
            'directors' => $movie['directors'],
            'genres' => $movie['genres'],
            'finished_at' => fake()->dateTimeBetween('-5 years', 'now'),
            'rating' => $rating,
            'is_favorite' => $rating === 5 && fake()->boolean(30),
            'comment' => fake()->boolean(40) ? fake()->sentence() : null,
        ];
    }
}
