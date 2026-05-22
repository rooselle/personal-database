<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Attributions')] class extends Component
{
    //
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="max-w-2xl">
        <flux:heading size="xl">{{ __('Attributions') }}</flux:heading>
        <flux:text class="mt-1 text-neutral-500 dark:text-zinc-400">
            {{ __('Credits and licences for third-party assets used in this app.') }}
        </flux:text>
    </div>

    <div class="max-w-2xl space-y-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-2">{{ __('Photography') }}</flux:heading>
            <flux:text class="text-neutral-600 dark:text-zinc-300">
                Homepage photo by
                <a href="https://unsplash.com/fr/@lifeof_peter_" target="_blank" class="font-medium underline underline-offset-4 hover:text-accent">
                    Peter Thomas
                </a>
                on
                <a href="https://unsplash.com" target="_blank" class="font-medium underline underline-offset-4 hover:text-accent">
                    Unsplash
                </a>.
            </flux:text>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-2">{{ __('Icons') }}</flux:heading>
            <flux:text class="text-neutral-600 dark:text-zinc-300">
                App icon (book stack) from
                <a href="https://www.flaticon.com/free-icons/book" target="_blank" class="font-medium underline underline-offset-4 hover:text-accent">
                    Flaticon
                </a>.
                Book icons created by
                <a href="https://www.flaticon.com/authors/smashicons" target="_blank" class="font-medium underline underline-offset-4 hover:text-accent">
                    Smashicons
                </a>
                — Flaticon.
            </flux:text>
        </div>
    </div>
</div>
