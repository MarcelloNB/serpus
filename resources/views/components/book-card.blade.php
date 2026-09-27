@props(['book'])

<div class="flex flex-col rounded-lg border border-slate-200 bg-white p-4 transition hover:border-brand hover:shadow-sm">
    @if ($book->cover_image)
        <img
            src="{{ asset('storage/'.$book->cover_image) }}"
            alt="Sampul {{ $book->title }}"
            class="mb-3 h-40 w-full rounded-md object-cover"
            loading="lazy"
        />
    @else
        <div class="mb-3 flex h-40 w-full items-center justify-center rounded-md bg-slate-100 text-ink/40">
            <x-icon name="book" class="h-10 w-10" />
        </div>
    @endif

    <h3 class="text-sm font-semibold leading-snug">
        <a href="{{ route('catalog.show', $book) }}" class="hover:text-ink/70">{{ $book->title }}</a>
    </h3>

    <p class="mt-1 text-xs text-ink/70">{{ $book->author }}</p>

    <div class="mt-3 flex items-center justify-between gap-2 text-xs">
        <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-ink/80">
            {{ $book->category?->name ?? 'Tanpa kategori' }}
        </span>

        <x-stock-badge :stock="$book->stock" />
    </div>
</div>
