<?php

namespace App\Models;

use Database\Factories\TvShowFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TvShow extends Model
{
    /** @use HasFactory<TvShowFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'cover_url',
        'year_released',
        'creators',
        'genres',
        'is_finished',
    ];

    protected function casts(): array
    {
        return [
            'year_released' => 'integer',
            'creators' => 'array',
            'genres' => 'array',
            'is_finished' => 'boolean',
        ];
    }

    /** @return HasMany<TvShowSeason, $this> */
    public function seasons(): HasMany
    {
        return $this->hasMany(TvShowSeason::class)->orderBy('season_number');
    }
}
