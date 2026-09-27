<div class="max-w-3xl">
    <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-ink/70 hover:text-ink">
        &larr; Kembali ke Katalog
    </a>

    <div class="mt-4 rounded-lg border border-slate-200 bg-white p-6">
        @if ($book->cover_image)
            <img
                src="{{ asset('storage/'.$book->cover_image) }}"
                alt="Sampul {{ $book->title }}"
                class="mb-4 h-64 w-full rounded-md object-cover"
            />
        @endif

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold">{{ $book->title }}</h2>
                <p class="mt-1 text-sm text-ink/70">Penulis: {{ $book->author }}</p>
                <p class="mt-1 text-sm text-ink/70">Kategori: {{ $book->category?->name ?? '—' }}</p>
            </div>

            <x-stock-badge :stock="$book->stock" />
        </div>

        <div class="mt-4 border-t border-slate-200 pt-4">
            <h3 class="text-sm font-semibold">Deskripsi</h3>
            <p class="mt-2 text-sm text-ink/70">{{ $book->description ?? 'Tidak ada deskripsi.' }}</p>
        </div>

        <div class="mt-6">
            <x-borrow-button :id="$book->id" :stock="$book->stock" />
        </div>
    </div>
</div>
