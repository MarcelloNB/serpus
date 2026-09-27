@if (($status ?? '') === 'dipinjam')
    <form
        method="POST"
        action="{{ route('admin.loans.return', $id) }}"
        onsubmit="return confirm('Tandai buku ini sudah dikembalikan?')"
    >
        @csrf
        @method('PATCH')

        <button
            type="submit"
            class="rounded-md border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50"
        >
            Kembalikan
        </button>
    </form>
@else
    <span class="text-xs text-ink/50">Selesai</span>
@endif
