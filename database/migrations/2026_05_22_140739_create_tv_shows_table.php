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
        Schema::create('tv_shows', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedSmallInteger('year_released');
            $table->json('creators');
            $table->json('genres');
            $table->boolean('is_finished')->default(false);
            $table->timestamps();

            $table->index('year_released');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_shows');
    }
};
