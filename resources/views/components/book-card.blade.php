@props(['book', 'rank' => null])

@php($spine = $book->spine_color)

<a
    href="{{ route('catalog.show', $book) }}"
    class="group block rounded-lg focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 focus:ring-offset-paper"
>
    <div
        class="relative aspect-[2/3] overflow-hidden rounded-lg shadow-md transition duration-200 group-hover:-translate-y-1.5 group-hover:shadow-xl"
        style="background-color: {{ $spine }}"
    >
        @if ($book->cover_image)
            <img
                src="{{ asset('storage/'.$book->cover_image) }}"
                alt="Sampul {{ $book->title }}"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                loading="lazy"
            />
        @else
            <div class="flex h-full w-full flex-col justify-between p-4" aria-hidden="true">
                <span class="h-1.5 w-12 rounded-full bg-paper/70"></span>
                <span class="font-display text-6xl font-semibold text-white">
                    {{ mb_strtoupper(mb_substr($book->title, 0, 1)) }}
                </span>
            </div>
        @endif

        @if ($rank)
            <span
                class="absolute left-3 top-2 font-display text-3xl font-semibold text-white drop-shadow-[0_1px_3px_rgba(0,0,0,0.6)]"
            >
                {{ str_pad((string) $rank, 2, '0', STR_PAD_LEFT) }}
            </span>
        @endif
    </div>

    <div class="mt-3">
        <h3 class="font-display text-sm font-semibold leading-snug transition group-hover:text-brand">
            {{ $book->title }}
        </h3>

        <p class="mt-1 text-xs text-ink/70">{{ $book->author }}</p>

        <div class="mt-2.5 flex items-center justify-between gap-2 text-xs">
            <span class="rounded-full px-2 py-0.5 font-medium text-ink" style="background-color: {{ $spine }}26">
                {{ $book->category?->name ?? 'Tanpa kategori' }}
            </span>

            <x-stock-badge :stock="$book->stock" />
        </div>
    </div>
</a>
