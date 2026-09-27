<div class="flex items-center gap-2">
    <a
        href="{{ route('admin.categories.edit', $id) }}"
        class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-ink hover:bg-slate-100"
    >
        Ubah
    </a>

    <x-confirm-delete :action="route('admin.categories.destroy', $id)" message="Hapus kategori ini?" />
</div>
