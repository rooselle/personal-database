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

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TranslationsPruneCommandTest extends TestCase
{
    private string $translationFile;

    private string $originalContent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->translationFile = lang_path('fr.json');
        $this->originalContent = File::get($this->translationFile);
    }

    protected function tearDown(): void
    {
        File::put($this->translationFile, $this->originalContent);

        parent::tearDown();
    }

    public function test_reports_no_unused_keys_when_all_are_used(): void
    {
        $this->artisan('translations:prune')
            ->expectsOutput('No unused translation keys.')
            ->assertExitCode(0);
    }

    public function test_removes_unused_keys(): void
    {
        $translations = json_decode(File::get($this->translationFile), associative: true);
        $translations['__unused_test_key__'] = 'Test value';
        File::put($this->translationFile, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->artisan('translations:prune')
            ->assertExitCode(0);

        $updatedTranslations = json_decode(File::get($this->translationFile), associative: true);
        $this->assertArrayNotHasKey('__unused_test_key__', $updatedTranslations);
    }

    public function test_preserves_used_keys(): void
    {
        $translations = json_decode(File::get($this->translationFile), associative: true);
        $translations['__unused_test_key__'] = 'Test value';
        File::put($this->translationFile, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->artisan('translations:prune')->assertExitCode(0);

        $updatedTranslations = json_decode(File::get($this->translationFile), associative: true);
        $this->assertArrayHasKey('Dashboard', $updatedTranslations);
        $this->assertArrayHasKey('Books', $updatedTranslations);
    }

    public function test_check_mode_succeeds_when_no_unused_keys(): void
    {
        $this->artisan('translations:prune --check')
            ->expectsOutput('No unused translation keys.')
            ->assertExitCode(0);
    }

    public function test_check_mode_fails_when_unused_keys_exist(): void
    {
        $translations = json_decode(File::get($this->translationFile), associative: true);
        $translations['__unused_test_key__'] = 'Test value';
        File::put($this->translationFile, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->artisan('translations:prune --check')
            ->assertExitCode(1);

        $unchanged = json_decode(File::get($this->translationFile), associative: true);
        $this->assertArrayHasKey('__unused_test_key__', $unchanged);
    }

    public function test_correctly_handles_escaped_apostrophes_in_keys(): void
    {
        $translations = json_decode(File::get($this->translationFile), associative: true);
        $this->assertArrayHasKey("Log every book you've read, with ratings, reviews, and your favourites.", $translations);
    }
}
