<x-data-table
    :url="route('catalog.data')"
    :columns="[
        ['data' => 'title', 'title' => 'Judul'],
        ['data' => 'author', 'title' => 'Penulis'],
        ['data' => 'category_name', 'title' => 'Kategori'],
        ['data' => 'stock', 'title' => 'Stok'],
        ['data' => 'aksi', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false],
    ]"
    :order="[['column' => 0, 'dir' => 'asc']]"
/>
