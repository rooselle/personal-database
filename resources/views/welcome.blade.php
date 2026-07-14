<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __(config('app.name')) }}</title>

        <link rel="icon" href="/images/book-stack.png" type="image/png">
        <link rel="apple-touch-icon" href="/images/book-stack.png">

        @fonts

        @vite(['resources/css/app.css'])
        @fluxAppearance
    </head>
    <body class="bg-paper-50 text-ink-900 min-h-screen flex flex-col">

        <header class="absolute inset-x-0 top-0 z-10 flex items-center justify-between px-6 py-4 lg:px-10">
            <div class="flex items-center gap-2">
                <img src="/images/book-stack.png" alt="Logo" class="size-7 object-contain" />
                <span class="font-heading font-semibold text-white drop-shadow">{{ __(config('app.name')) }}</span>
            </div>

        </header>

        {{-- Hero --}}
        <div class="relative h-[70vh] min-h-[420px] overflow-hidden">
            <img
                src="/images/hero.jpg"
                alt="{{ __('A pile of open books') }}"
                class="absolute inset-0 w-full h-full object-cover object-center"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-teal-900/75 via-teal-900/40 to-teal-900/75"></div>

            <div class="relative z-10 flex h-full items-center justify-center text-center px-6">
                <div>
                    <h1 class="font-display text-4xl font-normal text-white drop-shadow-lg sm:text-5xl lg:text-6xl">
                        {{ __(config('app.name')) }}
                    </h1>
                    <p class="mt-4 max-w-xl mx-auto text-lg text-white/85 drop-shadow">
                        {{ __('Your personal reading & watching journal — a digital home for the books you\'ve read and the shows and films you\'ve watched.') }}
                    </p>
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="mt-8 inline-block rounded-md border-thick border-hairline bg-coral-500 px-8 py-3 font-heading font-bold text-paper-fixed shadow-cutout-sm transition-all duration-fast ease-cocoon hover:-translate-y-0.5 hover:bg-coral-400 hover:shadow-cutout-md active:translate-x-0.5 active:translate-y-0.5 active:shadow-cutout-press"
                        >
                            {{ __('Go to dashboard') }}
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="mt-8 inline-block rounded-md border-thick border-hairline bg-coral-500 px-8 py-3 font-heading font-bold text-paper-fixed shadow-cutout-sm transition-all duration-fast ease-cocoon hover:-translate-y-0.5 hover:bg-coral-400 hover:shadow-cutout-md active:translate-x-0.5 active:translate-y-0.5 active:shadow-cutout-press"
                        >
                            {{ __('Sign in') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Features --}}
        <div class="flex-1 bg-paper-100 px-6 py-16 lg:px-10">
            <div class="max-w-4xl mx-auto grid gap-6 sm:grid-cols-3">
                <div class="rounded-lg border-thick border-hairline bg-paper-0 p-5 text-center shadow-cutout-sm">
                    <div class="inline-flex items-center justify-center size-12 rounded-lg bg-coral-100 dark:bg-zinc-700 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-coral-600 dark:text-coral-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-semibold text-ink-900">{{ __('Books') }}</h3>
                    <p class="mt-1 text-sm text-ink-500">{{ __('Log every book you\'ve read, with ratings, reviews, and your favourites.') }}</p>
                </div>
                <div class="rounded-lg border-thick border-hairline bg-paper-0 p-5 text-center shadow-cutout-sm">
                    <div class="inline-flex items-center justify-center size-12 rounded-lg bg-mustard-100 dark:bg-zinc-700 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-mustard-600 dark:text-mustard-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-semibold text-ink-900">{{ __('TV Shows') }}</h3>
                    <p class="mt-1 text-sm text-ink-500">{{ __('Track seasons and episodes across all the series you follow.') }}</p>
                </div>
                <div class="rounded-lg border-thick border-hairline bg-paper-0 p-5 text-center shadow-cutout-sm">
                    <div class="inline-flex items-center justify-center size-12 rounded-lg bg-teal-100 dark:bg-zinc-700 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-teal-700 dark:text-teal-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75.125v-3.375A2.25 2.25 0 0 1 4.5 13.5h15A2.25 2.25 0 0 1 21.75 15v3.375M5.25 4.5h13.5A1.875 1.875 0 0 1 20.625 6.375v7.875A1.875 1.875 0 0 1 18.75 16.125H5.25A1.875 1.875 0 0 1 3.375 14.25V6.375A1.875 1.875 0 0 1 5.25 4.5Z" />
                        </svg>
                    </div>
                    <h3 class="font-heading font-semibold text-ink-900">{{ __('Movies') }}</h3>
                    <p class="mt-1 text-sm text-ink-500">{{ __('Keep a personal record of every film you\'ve seen.') }}</p>
                </div>
            </div>
        </div>

        <footer class="bg-paper-100 border-t border-thick border-hairline px-6 py-4 text-center text-xs text-ink-300">
            @auth
                <a href="{{ route('dashboard') }}" class="hover:text-ink-700 transition-colors">{{ __('Dashboard') }}</a>
            @else
                <a href="{{ route('login') }}" class="hover:text-ink-700 transition-colors">{{ __('Sign in') }}</a>
            @endauth
            &nbsp;·&nbsp;
            {{ __('Photo by') }}
            <a href="https://unsplash.com/fr/@lifeof_peter_" target="_blank" class="hover:text-ink-700 transition-colors underline underline-offset-2">Peter Thomas</a>
            {{ __('on Unsplash') }}
        </footer>
    </body>
</html>
