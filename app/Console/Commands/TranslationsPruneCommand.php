<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TranslationsPruneCommand extends Command
{
    protected $signature = 'translations:prune {--check : Fail if unused keys exist instead of removing them}';

    protected $description = 'Remove unused translation keys from lang/fr.json';

    public function handle(): int
    {
        $translationFile = lang_path('fr.json');
        /** @var array<string, string> $translations */
        $translations = json_decode(File::get($translationFile), associative: true);

        $usedKeys = $this->findUsedKeys();
        $unusedKeys = array_values(array_diff(array_keys($translations), $usedKeys));

        if (empty($unusedKeys)) {
            $this->info('No unused translation keys.');

            return self::SUCCESS;
        }

        if ($this->option('check')) {
            $this->error(count($unusedKeys).' unused translation key(s) found in lang/fr.json:');
            foreach ($unusedKeys as $key) {
                $this->line("  - {$key}");
            }
            $this->line('Run <comment>php artisan translations:prune</comment> locally and commit the result.');

            return self::FAILURE;
        }

        foreach ($unusedKeys as $key) {
            unset($translations[$key]);
        }

        File::put($translationFile, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n");

        $this->warn('Removed '.count($unusedKeys).' unused translation key(s):');
        foreach ($unusedKeys as $key) {
            $this->line("  - {$key}");
        }

        return self::SUCCESS;
    }

    /** @return string[] */
    private function findUsedKeys(): array
    {
        $usedKeys = [];

        $directories = [app_path(), resource_path('views'), base_path('routes')];

        foreach ($directories as $directory) {
            if (! File::isDirectory($directory)) {
                continue;
            }

            foreach (File::allFiles($directory) as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $content = File::get($file->getPathname());

                preg_match_all('/__\(\s*\'((?:[^\'\\\\]|\\\\.)*)\'\s*[,)]/', $content, $singleQuotes);
                preg_match_all('/__\(\s*"((?:[^"\\\\]|\\\\.)*)"\s*[,)]/', $content, $doubleQuotes);

                $unescapedSingle = array_map(fn (string $k) => str_replace(["\\'", '\\\\'], ["'", '\\'], $k), $singleQuotes[1]);
                $unescapedDouble = array_map(fn (string $k) => str_replace(['\\"', '\\\\'], ['"', '\\'], $k), $doubleQuotes[1]);

                $usedKeys = [...$usedKeys, ...$unescapedSingle, ...$unescapedDouble];
            }
        }

        return array_unique($usedKeys);
    }
}
