<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BooksPageTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_books_page_requires_authentication(): void
    {
        $this->get(route('books'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_books_page(): void
    {
        $this->actingAsUser();

        $this->get(route('books'))->assertOk();
    }

    public function test_books_are_listed(): void
    {
        $this->actingAsUser();

        $book = Book::factory()->create(['title' => 'Normal People', 'author' => 'Sally Rooney']);

        Livewire::test('pages::books')
            ->assertSee('Normal People')
            ->assertSee('Sally Rooney');
    }

    public function test_books_can_be_searched_by_title(): void
    {
        $this->actingAsUser();

        Book::factory()->create(['title' => 'Crime and Punishment', 'author' => 'Fyodor Dostoevsky']);
        Book::factory()->create(['title' => 'Dune', 'author' => 'Frank Herbert']);

        Livewire::test('pages::books')
            ->set('search', 'Dune')
            ->assertSee('Dune')
            ->assertDontSeeText('Crime and Punishment');
    }

    public function test_books_can_be_searched_by_author(): void
    {
        $this->actingAsUser();

        Book::factory()->create(['title' => 'Crime and Punishment', 'author' => 'Fyodor Dostoevsky']);
        Book::factory()->create(['title' => 'Dune', 'author' => 'Frank Herbert']);

        Livewire::test('pages::books')
            ->set('search', 'Frank Herbert')
            ->assertSee('Dune')
            ->assertDontSeeText('Crime and Punishment');
    }

    public function test_book_can_be_added(): void
    {
        $this->actingAsUser();

        Livewire::test('pages::books')
            ->set('title', 'The Midnight Library')
            ->set('author', 'Matt Haig')
            ->set('publisher', 'Canongate')
            ->set('yearPublished', '2020')
            ->set('finishedAt', '2024-01-15')
            ->set('rating', '4')
            ->set('comment', 'Lovely read')
            ->call('saveBook')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('books', [
            'title' => 'The Midnight Library',
            'author' => 'Matt Haig',
            'publisher' => 'Canongate',
            'year_published' => 2020,
            'rating' => 4,
        ]);
    }

    public function test_adding_book_validates_required_fields(): void
    {
        $this->actingAsUser();

        Livewire::test('pages::books')
            ->call('saveBook')
            ->assertHasErrors(['title', 'author', 'publisher', 'yearPublished', 'finishedAt']);
    }

    public function test_book_rating_must_be_between_1_and_5(): void
    {
        $this->actingAsUser();

        Livewire::test('pages::books')
            ->set('rating', '6')
            ->call('saveBook')
            ->assertHasErrors(['rating']);
    }

    public function test_changing_rating_below_5_clears_favorite(): void
    {
        $this->actingAsUser();

        Livewire::test('pages::books')
            ->set('rating', '5')
            ->set('isFavorite', true)
            ->set('rating', '4')
            ->assertSet('isFavorite', false);
    }

    public function test_5_star_book_can_be_marked_as_favorite(): void
    {
        $this->actingAsUser();

        Livewire::test('pages::books')
            ->set('title', 'Fleabag')
            ->set('author', 'Phoebe Waller-Bridge')
            ->set('publisher', 'Oberon Books')
            ->set('yearPublished', '2019')
            ->set('finishedAt', '2024-03-01')
            ->set('rating', '5')
            ->set('isFavorite', true)
            ->call('saveBook')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('books', ['title' => 'Fleabag', 'is_favorite' => true]);
    }

    public function test_book_can_be_deleted(): void
    {
        $this->actingAsUser();

        $book = Book::factory()->create();

        Livewire::test('pages::books')
            ->call('deleteBook', $book->id)
            ->assertHasNoErrors();

        $this->assertModelMissing($book);
    }
}
