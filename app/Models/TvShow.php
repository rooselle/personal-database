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
