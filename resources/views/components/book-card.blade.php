@props(['book'])

<div class="flex flex-col rounded-lg border border-slate-200 bg-white p-4 transition hover:border-brand hover:shadow-sm">
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
