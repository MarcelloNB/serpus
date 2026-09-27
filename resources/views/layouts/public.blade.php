<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SerPus') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white font-sans antialiased text-ink">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ route('catalog.index') }}" class="text-lg font-semibold">SerPus</a>

                <nav class="flex items-center gap-2">
                    @guest
                        <a
                            href="{{ route('login') }}"
                            class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-50 focus:ring-2 focus:ring-brand"
                        >
                            Masuk
                        </a>
                        <a
                            href="{{ route('register') }}"
                            class="rounded-md bg-brand px-4 py-2 text-sm font-semibold text-ink hover:bg-brand/70 focus:ring-2 focus:ring-brand"
                        >
                            Daftar
                        </a>
                    @else
                        <a
                            href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('catalog.index') }}"
                            class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-slate-50 focus:ring-2 focus:ring-brand"
                        >
                            {{ auth()->user()->isAdmin() ? 'Dashboard' : 'Katalog' }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="rounded-md bg-brand px-4 py-2 text-sm font-semibold text-ink hover:bg-brand/70 focus:ring-2 focus:ring-brand"
                            >
                                Keluar
                            </button>
                        </form>
                    @endguest
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 text-xs text-ink/70 sm:px-6 lg:px-8">
                <span>SerPus — Sistem Peminjaman Buku</span>
                <span>&copy; {{ now()->year }}</span>
            </div>
        </footer>
    </body>
</html>
