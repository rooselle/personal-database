<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tv_show_seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tv_show_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('season_number');
            $table->unsignedTinyInteger('episode_count');
            $table->unsignedTinyInteger('watched_episodes')->default(0);
            $table->unsignedTinyInteger('rating')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['tv_show_id', 'season_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_show_seasons');
    }
};
