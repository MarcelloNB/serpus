<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-lg font-semibold">Pengguna</h1>
    </x-slot>

    <div class="mb-4 flex justify-end">
        <a
            href="{{ route('admin.users.create') }}"
            class="rounded-md bg-ink px-4 py-2 text-sm font-semibold text-white hover:bg-ink/90"
        >
            Tambah Pengguna
        </a>
    </div>

    <x-data-table
        :url="route('admin.users.data')"
        :columns="[
            ['data' => 'id', 'title' => 'ID'],
            ['data' => 'name', 'title' => 'Nama'],
            ['data' => 'email', 'title' => 'Email'],
            ['data' => 'role_label', 'title' => 'Peran'],
            ['data' => 'aksi', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false],
        ]"
        :order="[['column' => 1, 'dir' => 'asc']]"
    />
</x-app-layout>
