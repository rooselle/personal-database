<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    private static array $books = [
        ['title' => 'The Name of the Wind', 'author' => 'Patrick Rothfuss', 'publisher' => 'DAW Books', 'year' => 2007],
        ['title' => 'Normal People', 'author' => 'Sally Rooney', 'publisher' => 'Faber & Faber', 'year' => 2018],
        ['title' => 'Conversations with Friends', 'author' => 'Sally Rooney', 'publisher' => 'Faber & Faber', 'year' => 2017],
        ['title' => 'The Secret History', 'author' => 'Donna Tartt', 'publisher' => 'Knopf', 'year' => 1992],
        ['title' => 'Piranesi', 'author' => 'Susanna Clarke', 'publisher' => 'Bloomsbury', 'year' => 2020],
        ['title' => 'Eleanor Oliphant Is Completely Fine', 'author' => 'Gail Honeyman', 'publisher' => 'HarperCollins', 'year' => 2017],
        ['title' => 'The Midnight Library', 'author' => 'Matt Haig', 'publisher' => 'Canongate', 'year' => 2020],
        ['title' => 'Les Misérables', 'author' => 'Victor Hugo', 'publisher' => 'A. Lacroix, Verboeckhoven & Cie', 'year' => 1862],
        ['title' => 'Le Petit Prince', 'author' => 'Antoine de Saint-Exupéry', 'publisher' => 'Gallimard', 'year' => 1943],
        ['title' => 'La Peste', 'author' => 'Albert Camus', 'publisher' => 'Gallimard', 'year' => 1947],
        ['title' => 'Dune', 'author' => 'Frank Herbert', 'publisher' => 'Chilton Books', 'year' => 1965],
        ['title' => 'The Hitchhiker\'s Guide to the Galaxy', 'author' => 'Douglas Adams', 'publisher' => 'Pan Books', 'year' => 1979],
        ['title' => 'The Handmaid\'s Tale', 'author' => 'Margaret Atwood', 'publisher' => 'McClelland & Stewart', 'year' => 1985],
        ['title' => 'Never Let Me Go', 'author' => 'Kazuo Ishiguro', 'publisher' => 'Faber & Faber', 'year' => 2005],
        ['title' => 'The Alchemist', 'author' => 'Paulo Coelho', 'publisher' => 'HarperCollins', 'year' => 1988],
        ['title' => 'Beloved', 'author' => 'Toni Morrison', 'publisher' => 'Alfred A. Knopf', 'year' => 1987],
        ['title' => 'The Bell Jar', 'author' => 'Sylvia Plath', 'publisher' => 'Heinemann', 'year' => 1963],
        ['title' => '1984', 'author' => 'George Orwell', 'publisher' => 'Secker & Warburg', 'year' => 1949],
        ['title' => 'Educated', 'author' => 'Tara Westover', 'publisher' => 'Random House', 'year' => 2018],
        ['title' => 'Where the Crawdads Sing', 'author' => 'Delia Owens', 'publisher' => 'G.P. Putnam\'s Sons', 'year' => 2018],
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $book = self::$books[self::$index % count(self::$books)];
        self::$index++;
        $rating = fake()->numberBetween(1, 5);

        return [
            'title' => $book['title'],
            'year_published' => $book['year'],
            'author' => $book['author'],
            'publisher' => $book['publisher'],
            'finished_at' => fake()->dateTimeBetween('-5 years', 'now'),
            'rating' => $rating,
            'is_favorite' => $rating === 5 && fake()->boolean(30),
            'comment' => fake()->boolean(40) ? fake()->sentence() : null,
        ];
    }
}
