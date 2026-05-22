<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MoviesPageTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_movies_page_requires_authentication(): void
    {
        $this->get(route('movies'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_movies_page(): void
    {
        $this->actingAsUser();

        $this->get(route('movies'))->assertOk();
    }

    public function test_movies_are_listed(): void
    {
        $this->actingAsUser();

        Movie::factory()->create(['title' => 'Parasite', 'directors' => ['Bong Joon-ho']]);

        Livewire::test('pages::movies')
            ->assertSee('Parasite')
            ->assertSee('Bong Joon-ho');
    }

    public function test_movies_can_be_searched_by_title(): void
    {
        $this->actingAsUser();

        Movie::factory()->create(['title' => 'The Godfather']);
        Movie::factory()->create(['title' => 'Moonlight']);

        Livewire::test('pages::movies')
            ->set('search', 'Moonlight')
            ->assertSee('Moonlight')
            ->assertDontSeeText('The Godfather');
    }

    public function test_movie_can_be_added(): void
    {
        $this->actingAsUser();

        Livewire::test('pages::movies')
            ->set('title', 'Past Lives')
            ->set('directorsInput', 'Celine Song')
            ->set('genresInput', 'Drama, Romance')
            ->set('yearReleased', '2023')
            ->set('finishedAt', '2024-02-10')
            ->set('rating', '5')
            ->call('saveMovie')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('movies', [
            'title' => 'Past Lives',
            'year_released' => 2023,
            'rating' => 5,
        ]);
    }

    public function test_adding_movie_validates_required_fields(): void
    {
        $this->actingAsUser();

        Livewire::test('pages::movies')
            ->call('saveMovie')
            ->assertHasErrors(['title', 'directorsInput', 'genresInput', 'yearReleased', 'finishedAt']);
    }

    public function test_directors_are_stored_as_array_from_comma_separated_input(): void
    {
        $this->actingAsUser();

        Livewire::test('pages::movies')
            ->set('title', 'Everything Everywhere All at Once')
            ->set('directorsInput', 'Daniel Kwan, Daniel Scheinert')
            ->set('genresInput', 'Comedy')
            ->set('yearReleased', '2022')
            ->set('finishedAt', '2024-01-01')
            ->set('rating', '5')
            ->call('saveMovie');

        $movie = Movie::where('title', 'Everything Everywhere All at Once')->first();
        $this->assertCount(2, $movie->directors);
        $this->assertContains('Daniel Kwan', $movie->directors);
        $this->assertContains('Daniel Scheinert', $movie->directors);
    }

    public function test_movie_can_be_deleted(): void
    {
        $this->actingAsUser();

        $movie = Movie::factory()->create();

        Livewire::test('pages::movies')
            ->call('deleteMovie', $movie->id)
            ->assertHasNoErrors();

        $this->assertModelMissing($movie);
    }
}
