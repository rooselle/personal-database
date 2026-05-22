<?php

namespace Tests\Feature;

use App\Models\TvShow;
use App\Models\TvShowSeason;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TvShowsPageTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_tv_shows_page_requires_authentication(): void
    {
        $this->get(route('tv-shows'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_tv_shows_page(): void
    {
        $this->actingAsUser();

        $this->get(route('tv-shows'))->assertOk();
    }

    public function test_tv_shows_are_listed(): void
    {
        $this->actingAsUser();

        TvShow::factory()->create(['title' => 'Fleabag', 'creators' => ['Phoebe Waller-Bridge']]);

        Livewire::test('pages::tv-shows')
            ->assertSee('Fleabag')
            ->assertSee('Phoebe Waller-Bridge');
    }

    public function test_tv_shows_can_be_searched_by_title(): void
    {
        $this->actingAsUser();

        TvShow::factory()->create(['title' => 'Fleabag']);
        TvShow::factory()->create(['title' => 'Succession']);

        Livewire::test('pages::tv-shows')
            ->set('search', 'Succession')
            ->assertSee('Succession')
            ->assertDontSee('Fleabag');
    }

    public function test_show_can_be_added(): void
    {
        $this->actingAsUser();

        Livewire::test('pages::tv-shows')
            ->set('title', 'The Bear')
            ->set('creatorsInput', 'Christopher Storer')
            ->set('genresInput', 'Drama, Comedy')
            ->set('yearReleased', '2022')
            ->set('isFinished', false)
            ->call('saveShow')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tv_shows', [
            'title' => 'The Bear',
            'year_released' => 2022,
            'is_finished' => false,
        ]);
    }

    public function test_adding_show_validates_required_fields(): void
    {
        $this->actingAsUser();

        Livewire::test('pages::tv-shows')
            ->call('saveShow')
            ->assertHasErrors(['title', 'creatorsInput', 'genresInput', 'yearReleased']);
    }

    public function test_show_can_be_deleted_with_its_seasons(): void
    {
        $this->actingAsUser();

        $show = TvShow::factory()->create();
        $season = TvShowSeason::factory()->create(['tv_show_id' => $show->id, 'season_number' => 1]);

        Livewire::test('pages::tv-shows')
            ->call('deleteShow', $show->id);

        $this->assertModelMissing($show);
        $this->assertModelMissing($season);
    }

    public function test_season_can_be_added_to_show(): void
    {
        $this->actingAsUser();

        $show = TvShow::factory()->create();

        Livewire::test('pages::tv-shows')
            ->set('selectedShowId', $show->id)
            ->set('seasonNumber', '1')
            ->set('episodeCount', '8')
            ->set('watchedEpisodes', '8')
            ->set('seasonRating', '5')
            ->set('seasonComment', 'Masterpiece')
            ->call('saveSeason')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tv_show_seasons', [
            'tv_show_id' => $show->id,
            'season_number' => 1,
            'episode_count' => 8,
            'watched_episodes' => 8,
            'rating' => 5,
        ]);
    }

    public function test_adding_season_validates_required_fields(): void
    {
        $this->actingAsUser();

        $show = TvShow::factory()->create();

        Livewire::test('pages::tv-shows')
            ->set('selectedShowId', $show->id)
            ->call('saveSeason')
            ->assertHasErrors(['seasonNumber', 'episodeCount']);
    }

    public function test_season_can_be_deleted(): void
    {
        $this->actingAsUser();

        $show = TvShow::factory()->create();
        $season = TvShowSeason::factory()->create(['tv_show_id' => $show->id, 'season_number' => 1]);

        Livewire::test('pages::tv-shows')
            ->set('selectedShowId', $show->id)
            ->call('deleteSeason', $season->id);

        $this->assertModelMissing($season);
    }

    public function test_changing_season_rating_below_5_clears_favorite(): void
    {
        $this->actingAsUser();

        Livewire::test('pages::tv-shows')
            ->set('seasonRating', '5')
            ->set('seasonIsFavorite', true)
            ->set('seasonRating', '4')
            ->assertSet('seasonIsFavorite', false);
    }
}
