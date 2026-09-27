<div class="flex items-center gap-2">
    <a
        href="{{ route('admin.users.edit', $id) }}"
        class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-ink hover:bg-slate-100"
    >
        Ubah
    </a>

    @if ((int) auth()->id() !== (int) $id)
        <x-confirm-delete :action="route('admin.users.destroy', $id)" message="Hapus pengguna ini?" />
    @endif
</div>
