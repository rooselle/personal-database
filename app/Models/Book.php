<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'cover_url',
        'year_published',
        'author',
        'publisher',
        'finished_at',
        'rating',
        'is_favorite',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'finished_at' => 'date',
            'year_published' => 'integer',
            'rating' => 'integer',
            'is_favorite' => 'boolean',
        ];
    }
}
