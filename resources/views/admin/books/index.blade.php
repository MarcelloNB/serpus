<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-lg font-semibold">Buku</h1>
    </x-slot>

    <div class="mb-4 flex justify-end">
        <a
            href="{{ route('admin.books.create') }}"
            class="rounded-md bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand/90"
        >
            Tambah Buku
        </a>
    </div>

    <x-data-table
        :url="route('admin.books.data')"
        :columns="[
            ['data' => 'id', 'title' => 'ID'],
            ['data' => 'title', 'title' => 'Judul'],
            ['data' => 'author', 'title' => 'Penulis'],
            ['data' => 'category_name', 'title' => 'Kategori'],
            ['data' => 'stock', 'title' => 'Stok'],
            ['data' => 'aksi', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false],
        ]"
        :order="[['column' => 1, 'dir' => 'asc']]"
    />
</x-app-layout>
