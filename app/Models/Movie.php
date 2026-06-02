<?php

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
