<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <link rel="icon" href="/images/book-stack.png" type="image/png">
        <link rel="apple-touch-icon" href="/images/book-stack.png">

        @fonts

        @vite(['resources/css/app.css'])
        @fluxAppearance
    </head>
    <body class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 min-h-screen flex flex-col">

        <header class="absolute inset-x-0 top-0 z-10 flex items-center justify-between px-6 py-4 lg:px-10">
            <div class="flex items-center gap-2">
                <img src="/images/book-stack.png" alt="Logo" class="size-7 object-contain" />
                <span class="font-semibold text-white drop-shadow">{{ config('app.name') }}</span>
            </div>

            @if (Route::has('login'))
                <nav class="flex items-center gap-3">
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-block px-5 py-1.5 bg-white/20 hover:bg-white/30 backdrop-blur text-white border border-white/30 rounded-md text-sm leading-normal transition-colors"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 text-white hover:text-white/80 border border-transparent hover:border-white/30 rounded-md text-sm leading-normal transition-colors"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 bg-white/20 hover:bg-white/30 backdrop-blur text-white border border-white/30 rounded-md text-sm leading-normal transition-colors">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        {{-- Hero --}}
        <div class="relative h-[70vh] min-h-[420px] overflow-hidden">
            <img
                src="/images/hero.jpg"
                alt="A pile of open books"
                class="absolute inset-0 w-full h-full object-cover object-center"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/30 to-black/60"></div>

            <div class="relative z-10 flex h-full items-center justify-center text-center px-6">
                <div>
                    <h1 class="text-4xl font-bold text-white drop-shadow-lg sm:text-5xl lg:text-6xl">
                        {{ config('app.name') }}
                    </h1>
                    <p class="mt-4 max-w-xl mx-auto text-lg text-white/85 drop-shadow">
                        Your personal reading & watching journal — a digital home for the books you've read and the shows and films you've watched.
                    </p>
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="mt-8 inline-block px-8 py-3 bg-white text-zinc-900 font-semibold rounded-lg hover:bg-zinc-100 transition-colors shadow-lg"
                        >
                            Go to dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="mt-8 inline-block px-8 py-3 bg-white text-zinc-900 font-semibold rounded-lg hover:bg-zinc-100 transition-colors shadow-lg"
                        >
                            Sign in
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Features --}}
        <div class="flex-1 bg-zinc-50 dark:bg-zinc-900 px-6 py-16 lg:px-10">
            <div class="max-w-4xl mx-auto grid gap-8 sm:grid-cols-3">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center size-12 rounded-xl bg-zinc-200 dark:bg-zinc-800 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-zinc-600 dark:text-zinc-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Books</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Log every book you've read, with ratings, reviews, and your favourites.</p>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center size-12 rounded-xl bg-zinc-200 dark:bg-zinc-800 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-zinc-600 dark:text-zinc-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">TV Shows</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Track seasons and episodes across all the series you follow.</p>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center size-12 rounded-xl bg-zinc-200 dark:bg-zinc-800 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-zinc-600 dark:text-zinc-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75.125v-3.375A2.25 2.25 0 0 1 4.5 13.5h15A2.25 2.25 0 0 1 21.75 15v3.375M5.25 4.5h13.5A1.875 1.875 0 0 1 20.625 6.375v7.875A1.875 1.875 0 0 1 18.75 16.125H5.25A1.875 1.875 0 0 1 3.375 14.25V6.375A1.875 1.875 0 0 1 5.25 4.5Z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Movies</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Keep a personal record of every film you've seen.</p>
                </div>
            </div>
        </div>

        <footer class="bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 px-6 py-4 text-center text-xs text-zinc-400 dark:text-zinc-600">
            <a href="{{ route('login') }}" class="hover:text-zinc-600 dark:hover:text-zinc-400 transition-colors">Sign in</a>
            &nbsp;·&nbsp;
            Photo by
            <a href="https://unsplash.com/fr/@lifeof_peter_" target="_blank" class="hover:text-zinc-600 dark:hover:text-zinc-400 transition-colors underline underline-offset-2">Peter Thomas</a>
            on Unsplash
        </footer>
    </body>
</html>
