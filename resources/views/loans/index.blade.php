<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-lg font-semibold">Riwayat Peminjaman</h1>
    </x-slot>

    <x-data-table
        :url="route('loans.data')"
        :columns="[
            ['data' => 'book_title', 'title' => 'Buku'],
            ['data' => 'borrowed_at_label', 'title' => 'Tanggal Pinjam'],
            ['data' => 'due_at_label', 'title' => 'Batas Kembali', 'orderable' => false],
            ['data' => 'returned_at_label', 'title' => 'Tanggal Kembali'],
            ['data' => 'status_label', 'title' => 'Status', 'orderable' => false],
        ]"
        :order="[['column' => 1, 'dir' => 'desc']]"
    />
</x-app-layout>
