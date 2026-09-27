import Alpine from 'alpinejs';
import DataTable from 'datatables.net';
import 'datatables.net-dt/css/dataTables.dataTables.css';

window.Alpine = Alpine;

Alpine.start();

document.querySelectorAll('table[data-datatable]').forEach((table) => {
    new DataTable(table, {
        serverSide: true,
        processing: true,
        ajax: table.dataset.url,
        pageLength: Number(table.dataset.pageLength) || 10,
        lengthMenu: [10, 25, 50, 100],
        order: JSON.parse(table.dataset.order || '[]'),
        columns: JSON.parse(table.dataset.columns || '[]'),
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty: 'Belum ada data',
            infoFiltered: '(disaring dari _MAX_ data)',
            zeroRecords: 'Data tidak ditemukan',
            processing: 'Memuat data…',
            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Berikutnya',
                previous: 'Sebelumnya',
            },
        },
    });
});
