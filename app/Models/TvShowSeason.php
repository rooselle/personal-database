<?php

namespace App\Models;

use Database\Factories\TvShowSeasonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TvShowSeason extends Model
{
    /** @use HasFactory<TvShowSeasonFactory> */
    use HasFactory;

    protected $fillable = [
        'tv_show_id',
        'season_number',
        'episode_count',
        'watched_episodes',
        'rating',
        'is_favorite',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'season_number' => 'integer',
            'episode_count' => 'integer',
            'watched_episodes' => 'integer',
            'rating' => 'integer',
            'is_favorite' => 'boolean',
        ];
    }

    public function tvShow(): BelongsTo
    {
        return $this->belongsTo(TvShow::class);
    }

    public function isFullyWatched(): bool
    {
        return $this->watched_episodes >= $this->episode_count;
    }
}
