<aside
    x-cloak
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-forest text-paper transition-transform duration-200 lg:translate-x-0"
>
    <a
        href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('catalog.index') }}"
        class="flex h-16 items-center gap-3 border-b border-white/10 px-6"
    >
        <span class="flex h-8 w-8 items-center justify-center rounded-md bg-brand text-sm font-bold text-white">S</span>
        <span class="font-display text-xl font-semibold tracking-wide text-paper">SerPus</span>
    </a>

    <nav class="flex-1 space-y-1 px-3 py-4 text-sm">
        @if (auth()->user()->isAdmin())
            <a
                href="{{ route('admin.dashboard') }}"
                @class([
                    'flex items-center gap-2 rounded-md px-3 py-2 font-medium transition',
                    'bg-brand text-white' => request()->routeIs('admin.dashboard'),
                    'text-paper hover:bg-white/10' => ! request()->routeIs('admin.dashboard'),
                ])
            >
                <x-icon name="home" />
                Dashboard
            </a>

            <a
                href="{{ route('admin.books.index') }}"
                @class([
                    'flex items-center gap-2 rounded-md px-3 py-2 font-medium transition',
                    'bg-brand text-white' => request()->routeIs('admin.books.*'),
                    'text-paper hover:bg-white/10' => ! request()->routeIs('admin.books.*'),
                ])
            >
                <x-icon name="book" />
                Buku
            </a>

            <a
                href="{{ route('admin.categories.index') }}"
                @class([
                    'flex items-center gap-2 rounded-md px-3 py-2 font-medium transition',
                    'bg-brand text-white' => request()->routeIs('admin.categories.*'),
                    'text-paper hover:bg-white/10' => ! request()->routeIs('admin.categories.*'),
                ])
            >
                <x-icon name="category" />
                Kategori
            </a>

            <a
                href="{{ route('admin.users.index') }}"
                @class([
                    'flex items-center gap-2 rounded-md px-3 py-2 font-medium transition',
                    'bg-brand text-white' => request()->routeIs('admin.users.*'),
                    'text-paper hover:bg-white/10' => ! request()->routeIs('admin.users.*'),
                ])
            >
                <x-icon name="users" />
                Pengguna
            </a>

            <a
                href="{{ route('admin.loans.index') }}"
                @class([
                    'flex items-center gap-2 rounded-md px-3 py-2 font-medium transition',
                    'bg-brand text-white' => request()->routeIs('admin.loans.*'),
                    'text-paper hover:bg-white/10' => ! request()->routeIs('admin.loans.*'),
                ])
            >
                <x-icon name="loans" />
                Peminjaman
            </a>

            <a
                href="{{ route('catalog.index') }}"
                @class([
                    'flex items-center gap-2 rounded-md px-3 py-2 font-medium transition',
                    'bg-brand text-white' => request()->routeIs('catalog.index', 'catalog.show'),
                    'text-paper hover:bg-white/10' => ! request()->routeIs('catalog.index', 'catalog.show'),
                ])
            >
                <x-icon name="catalog" />
                Katalog Buku
            </a>
        @else
            <a
                href="{{ route('catalog.index') }}"
                @class([
                    'flex items-center gap-2 rounded-md px-3 py-2 font-medium transition',
                    'bg-brand text-white' => request()->routeIs('catalog.index', 'catalog.show'),
                    'text-paper hover:bg-white/10' => ! request()->routeIs('catalog.index', 'catalog.show'),
                ])
            >
                <x-icon name="catalog" />
                Katalog Buku
            </a>

            <a
                href="{{ route('loans.index') }}"
                @class([
                    'flex items-center gap-2 rounded-md px-3 py-2 font-medium transition',
                    'bg-brand text-white' => request()->routeIs('loans.*'),
                    'text-paper hover:bg-white/10' => ! request()->routeIs('loans.*'),
                ])
            >
                <x-icon name="history" />
                Riwayat Peminjaman
            </a>
        @endif
    </nav>

    <div class="border-t border-white/10 px-6 py-4 text-xs text-white/70">
        Masuk sebagai
        <span class="font-semibold text-white">{{ auth()->user()->role->label() }}</span>
    </div>
</aside>
