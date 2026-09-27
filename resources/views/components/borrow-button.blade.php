@if ((int) $stock < 1)
    <button
        type="button"
        disabled
        aria-disabled="true"
        class="inline-flex cursor-not-allowed rounded-md bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-500"
    >
        Stok habis
    </button>
@elseif (auth()->check())
    <form method="POST" action="{{ route('catalog.borrow', $id) }}">
        @csrf

        <button
            type="submit"
            class="inline-flex rounded-md bg-brand px-4 py-2 text-sm font-semibold text-ink hover:bg-brand/70 focus:ring-2 focus:ring-brand"
        >
            Pinjam
        </button>
    </form>
@else
    <a
        href="{{ route('login') }}"
        class="inline-flex rounded-md bg-brand px-4 py-2 text-sm font-semibold text-ink hover:bg-brand/70 focus:ring-2 focus:ring-brand"
    >
        Masuk untuk meminjam
    </a>
@endif
