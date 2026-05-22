<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('/books', 'pages::books')->name('books');
    Route::livewire('/movies', 'pages::movies')->name('movies');
    Route::livewire('/tv-shows', 'pages::tv-shows')->name('tv-shows');
});

require __DIR__.'/settings.php';
