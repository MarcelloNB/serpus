<form method="GET" action="{{ route('catalog.index') }}" data-catalog-form class="space-y-4">
    <div class="flex flex-wrap items-end gap-3">
        <div class="relative min-w-64 flex-1">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-ink/50">
                <x-icon name="search" class="h-4 w-4" />
            </span>

            <input
                type="search"
                name="search[value]"
                data-catalog-search
                data-url="{{ route('catalog.data') }}"
                value="{{ request('search.value') }}"
                placeholder="Cari judul atau penulis…"
                aria-label="Cari buku"
                class="w-full rounded-md border-slate-300 py-2 pl-9 pr-3 text-sm placeholder:text-ink/50 focus:border-ink focus:ring-brand"
            >
        </div>

        <div class="flex flex-col gap-1">
            <label for="filter-kategori" class="text-xs font-medium text-ink/70">Kategori</label>
            <select
                id="filter-kategori"
                name="kategori"
                data-catalog-filter
                class="rounded-md border-slate-300 text-sm focus:border-brand focus:ring-brand"
            >
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option
                        value="{{ $category->id }}"
                        @selected((string) request('kategori') === (string) $category->id)
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <label for="filter-stok" class="text-xs font-medium text-ink/70">Stok</label>
            <select
                id="filter-stok"
                name="stok"
                data-catalog-filter
                class="rounded-md border-slate-300 text-sm focus:border-brand focus:ring-brand"
            >
                <option value="">Semua stok</option>
                <option value="tersedia" @selected(request('stok') === 'tersedia')>Tersedia</option>
                <option value="habis" @selected(request('stok') === 'habis')>Stok habis</option>
            </select>
        </div>

        <button
            type="submit"
            class="rounded-md bg-ink px-4 py-2 text-sm font-semibold text-white hover:bg-ink/90 focus:ring-2 focus:ring-brand"
        >
            Terapkan
        </button>
    </div>

    <div data-catalog-grid class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse ($books as $book)
            <x-book-card :book="$book" />
        @empty
            <x-empty-state
                message="Belum ada buku di katalog."
                class="col-span-full rounded-lg border border-dashed border-slate-300"
            />
        @endforelse
    </div>
</form>
