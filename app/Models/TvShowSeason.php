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

    /** @return BelongsTo<TvShow, $this> */
    public function tvShow(): BelongsTo
    {
        return $this->belongsTo(TvShow::class);
    }

    public function isFullyWatched(): bool
    {
        return $this->watched_episodes >= $this->episode_count;
    }
}
