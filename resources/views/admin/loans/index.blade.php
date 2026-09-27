<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-lg font-semibold">Peminjaman</h1>
    </x-slot>

    <div class="mb-4 flex justify-end">
        <a
            href="{{ route('admin.loans.create') }}"
            class="rounded-md bg-ink px-4 py-2 text-sm font-semibold text-white hover:bg-ink/90"
        >
            Tambah Peminjaman
        </a>
    </div>

    <x-data-table
        :url="route('admin.loans.data')"
        :columns="[
            ['data' => 'id', 'title' => 'ID'],
            ['data' => 'user_name', 'title' => 'Peminjam'],
            ['data' => 'book_title', 'title' => 'Buku'],
            ['data' => 'borrowed_at_label', 'title' => 'Dipinjam Pada'],
            ['data' => 'returned_at_label', 'title' => 'Dikembalikan Pada'],
            ['data' => 'status_label', 'title' => 'Status', 'orderable' => false],
            ['data' => 'aksi', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false],
        ]"
        :order="[['column' => 3, 'dir' => 'desc']]"
    />
</x-app-layout>
