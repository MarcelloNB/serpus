<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SerPus') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:400,500,600,700|plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-screen flex-col bg-paper font-sans antialiased text-ink">
        <header class="bg-forest text-paper">
            <div class="mx-auto flex min-h-16 max-w-7xl flex-wrap items-center justify-between gap-x-4 gap-y-2 px-4 py-2 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md bg-brand text-sm font-bold text-white">S</span>
                        <span class="text-lg font-semibold tracking-wide text-white">SerPus</span>
                    </a>

                    <nav class="flex items-center gap-1 text-sm">
                        <a
                            href="{{ route('home') }}"
                            @class([
                                'rounded-md px-3 py-2 font-medium transition',
                                'bg-brand text-white' => request()->routeIs('home'),
                                'text-paper hover:bg-white/10' => ! request()->routeIs('home'),
                            ])
                        >
                            Beranda
                        </a>
                        <a
                            href="{{ route('catalog.index') }}"
                            @class([
                                'rounded-md px-3 py-2 font-medium transition',
                                'bg-brand text-white' => request()->routeIs('catalog.*'),
                                'text-paper hover:bg-white/10' => ! request()->routeIs('catalog.*'),
                            ])
                        >
                            Katalog
                        </a>
                    </nav>
                </div>

                <nav class="flex items-center gap-2">
                    @guest
                        <a
                            href="{{ route('login') }}"
                            class="rounded-md border border-white/30 px-4 py-2 text-sm font-semibold hover:bg-white/10 focus:ring-2 focus:ring-brand"
                        >
                            Masuk
                        </a>
                        <a
                            href="{{ route('register') }}"
                            class="rounded-md bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand/90 focus:ring-2 focus:ring-brand"
                        >
                            Daftar
                        </a>
                    @else
                        <a
                            href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('catalog.index') }}"
                            class="rounded-md border border-white/30 px-4 py-2 text-sm font-semibold hover:bg-white/10 focus:ring-2 focus:ring-brand"
                        >
                            {{ auth()->user()->isAdmin() ? 'Dashboard' : 'Katalog' }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="rounded-md bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand/90 focus:ring-2 focus:ring-brand"
                            >
                                Keluar
                            </button>
                        </form>
                    @endguest
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        <footer class="bg-forest">
            <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 text-xs font-medium text-paper/80 sm:px-6 lg:px-8">
                <span>SerPus — Sistem Elektronik Perpustakaan</span>
                <span>&copy; {{ now()->year }}</span>
            </div>
        </footer>
    </body>
</html>
