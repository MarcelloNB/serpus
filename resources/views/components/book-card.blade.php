@props(['book'])

@php($spine = $book->spine_color)

<div class="flex flex-col rounded-lg border border-forest/10 bg-white p-4 transition hover:border-brand/60 hover:shadow-md">
    @if ($book->cover_image)
        <img
            src="{{ asset('storage/'.$book->cover_image) }}"
            alt="Sampul {{ $book->title }}"
            class="mb-3 h-40 w-full rounded-md object-cover shadow-sm"
            loading="lazy"
        />
    @else
        <div
            class="mb-3 flex h-40 w-full flex-col justify-between rounded-md p-3"
            style="background-color: {{ $spine }}"
            aria-hidden="true"
        >
            <span class="h-1.5 w-10 rounded-full bg-paper/70"></span>
            <span class="font-display text-4xl font-semibold text-paper">
                {{ mb_strtoupper(mb_substr($book->title, 0, 1)) }}
            </span>
        </div>
    @endif

    <h3 class="font-display text-sm font-semibold leading-snug">
        <a href="{{ route('catalog.show', $book) }}" class="hover:text-brand">{{ $book->title }}</a>
    </h3>

    <p class="mt-1 text-xs text-ink/70">{{ $book->author }}</p>

    <div class="mt-3 flex items-center justify-between gap-2 text-xs">
        <span class="rounded-full px-2 py-0.5 font-medium text-ink" style="background-color: {{ $spine }}26">
            {{ $book->category?->name ?? 'Tanpa kategori' }}
        </span>

        <x-stock-badge :stock="$book->stock" />
    </div>
</div>
