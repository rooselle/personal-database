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

use Database\Factories\MovieFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    /** @use HasFactory<MovieFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'cover_url',
        'year_released',
        'directors',
        'genres',
        'finished_at',
        'rating',
        'is_favorite',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'finished_at' => 'date',
            'year_released' => 'integer',
            'directors' => 'array',
            'genres' => 'array',
            'rating' => 'integer',
            'is_favorite' => 'boolean',
        ];
    }
}
