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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedSmallInteger('year_released');
            $table->json('directors');
            $table->json('genres');
            $table->date('finished_at');
            $table->unsignedTinyInteger('rating');
            $table->boolean('is_favorite')->default(false);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index('finished_at');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
