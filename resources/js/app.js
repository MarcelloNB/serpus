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

const catalogSearch = document.querySelector('[data-catalog-search]');
const catalogGrid = document.querySelector('[data-catalog-grid]');

if (catalogSearch && catalogGrid) {
    let searchTimer;

    catalogSearch.addEventListener('input', () => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(() => searchBooks(catalogSearch.value), 300);
    });
}

async function searchBooks(keyword) {
    const url = new URL(catalogSearch.dataset.url, window.location.origin);

    url.searchParams.set('search[value]', keyword);
    url.searchParams.set('length', '100');

    const response = await fetch(url);
    const payload = await response.json();

    renderBooks(payload.data);
}

function renderBooks(books) {
    if (books.length === 0) {
        catalogGrid.innerHTML =
            '<div class="col-span-full rounded-lg border border-dashed border-slate-300 px-6 py-12 text-center">' +
            '<p class="text-sm font-semibold text-ink">Buku tidak ditemukan.</p>' +
            '</div>';

        return;
    }

    catalogGrid.innerHTML = books
        .map(
            (book) => `
                <div class="flex flex-col rounded-lg border border-slate-200 bg-white p-4 transition hover:border-brand hover:shadow-sm">
                    <h3 class="text-sm font-semibold leading-snug">${book.title}</h3>
                    <p class="mt-1 text-xs text-ink/70">${escapeHtml(book.author)}</p>
                    <div class="mt-3 flex items-center justify-between gap-2 text-xs">
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-ink/80">${escapeHtml(book.category_name ?? 'Tanpa kategori')}</span>
                        ${book.stock}
                    </div>
                </div>`,
        )
        .join('');
}

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}
