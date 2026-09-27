<div class="flex items-center gap-2">
    <a
        href="{{ route('admin.categories.edit', $id) }}"
        class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-ink hover:bg-slate-100"
    >
        Ubah
    </a>

    <form method="POST" action="{{ route('admin.categories.destroy', $id) }}" onsubmit="return confirm('Hapus kategori ini?')">
        @csrf
        @method('DELETE')

        <button
            type="submit"
            class="rounded-md border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50"
        >
            Hapus
        </button>
    </form>
</div>
