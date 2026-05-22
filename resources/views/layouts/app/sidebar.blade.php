<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('My Library')" class="grid">
                    <flux:sidebar.item icon="book-open" :href="route('books')" :current="request()->routeIs('books')" wire:navigate>
                        {{ __('Books') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="film" :href="route('movies')" :current="request()->routeIs('movies')" wire:navigate>
                        {{ __('Movies') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="tv" :href="route('tv-shows')" :current="request()->routeIs('tv-shows')" wire:navigate>
                        {{ __('TV Shows') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="information-circle" :href="route('attributions')" :current="request()->routeIs('attributions')" wire:navigate>
                    {{ __('Attributions') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <div x-data class="hidden lg:block px-2 pb-1">
                <button
                    type="button"
                    x-on:click="$flux.appearance = $flux.appearance === 'dark' ? 'light' : 'dark'"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-zinc-600 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
                >
                    <flux:icon.sun x-show="$flux.appearance === 'dark'" class="size-5 shrink-0" />
                    <flux:icon.moon x-show="$flux.appearance !== 'dark'" class="size-5 shrink-0" />
                    <span x-text="$flux.appearance === 'dark' ? 'Light mode' : 'Dark mode'"></span>
                </button>
            </div>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:button x-data icon="sun" x-show="$flux.appearance === 'dark'" x-on:click="$flux.appearance = 'light'" variant="ghost" size="sm" />
            <flux:button x-data icon="moon" x-show="$flux.appearance !== 'dark'" x-on:click="$flux.appearance = 'dark'" variant="ghost" size="sm" />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
