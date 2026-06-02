<?php

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
