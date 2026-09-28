@php($spine = $book->spine_color)

<div class="mx-auto max-w-5xl">
    <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-ink/70 hover:text-ink">
        &larr; Kembali ke Katalog
    </a>

    <section class="mt-6 overflow-hidden rounded-2xl bg-sand shadow-lg ring-1 ring-forest/10">
        <div class="grid gap-8 p-6 sm:p-10 md:grid-cols-[240px_1fr] md:items-start">
            <div class="mx-auto w-44 sm:w-52 md:w-full">
                @if ($book->cover_image)
                    <img
                        src="{{ asset('storage/'.$book->cover_image) }}"
                        alt="Sampul {{ $book->title }}"
                        class="aspect-[2/3] w-full -rotate-2 rounded-lg object-cover shadow-xl ring-1 ring-forest/10"
                        loading="lazy"
                    />
                @else
                    <div
                        class="flex aspect-[2/3] w-full -rotate-2 flex-col justify-between rounded-lg p-5 shadow-xl"
                        style="background-color: {{ $spine }}"
                        aria-hidden="true"
                    >
                        <span class="h-1.5 w-12 rounded-full bg-paper/70"></span>
                        <span class="font-display text-6xl font-semibold text-white">
                            {{ mb_strtoupper(mb_substr($book->title, 0, 1)) }}
                        </span>
                    </div>
                @endif
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-forest">
                    {{ $book->category?->name ?? 'Tanpa kategori' }}
                </p>

                <h2 class="font-display mt-3 text-3xl font-semibold leading-tight text-ink sm:text-4xl">
                    {{ $book->title }}
                </h2>

                <dl class="mt-6 border-t border-forest/15 text-sm">
                    <div class="flex items-center justify-between gap-4 border-b border-forest/10 py-3">
                        <dt class="text-xs font-semibold uppercase tracking-widest text-ink/50">Penulis</dt>
                        <dd class="text-ink">{{ $book->author }}</dd>
                    </div>

                    <div class="flex items-center justify-between gap-4 border-b border-forest/10 py-3">
                        <dt class="text-xs font-semibold uppercase tracking-widest text-ink/50">Kategori</dt>
                        <dd class="text-ink">{{ $book->category?->name ?? '—' }}</dd>
                    </div>

                    <div class="flex items-center justify-between gap-4 border-b border-forest/10 py-3">
                        <dt class="text-xs font-semibold uppercase tracking-widest text-ink/50">Status</dt>
                        <dd><x-stock-badge :stock="$book->stock" /></dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <x-borrow-button :id="$book->id" :stock="$book->stock" />
                </div>
            </div>
        </div>
    </section>

    <section class="mt-8">
        <div class="flex items-center gap-4">
            <h3 class="font-display text-lg font-semibold text-ink">Deskripsi</h3>
            <span class="h-px flex-1 bg-forest/15"></span>
        </div>

        <p class="mt-4 max-w-3xl text-sm leading-relaxed text-ink/70">
            {{ $book->description ?? 'Tidak ada deskripsi.' }}
        </p>
    </section>
</div>
