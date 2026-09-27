<div class="space-y-4">
    <div class="relative max-w-md">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-ink/50">
            <x-icon name="search" class="h-4 w-4" />
        </span>

        <input
            type="search"
            data-catalog-search
            data-url="{{ route('catalog.data') }}"
            placeholder="Cari judul atau penulis…"
            aria-label="Cari buku"
            class="w-full rounded-md border-slate-300 py-2 pl-9 pr-3 text-sm placeholder:text-ink/50 focus:border-ink focus:ring-brand"
        >
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
</div>
