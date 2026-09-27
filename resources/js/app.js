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

const catalogForm = document.querySelector('[data-catalog-form]');
const catalogSearch = document.querySelector('[data-catalog-search]');
const catalogGrid = document.querySelector('[data-catalog-grid]');

if (catalogForm && catalogSearch && catalogGrid) {
    let searchTimer;

    catalogSearch.addEventListener('input', () => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(() => fetchBooks(), 300);
    });

    catalogForm.querySelectorAll('[data-catalog-filter]').forEach((filter) => {
        filter.addEventListener('change', () => fetchBooks());
    });

    catalogForm.addEventListener('submit', (event) => {
        event.preventDefault();
        fetchBooks();
    });
}

async function fetchBooks() {
    const url = new URL(catalogSearch.dataset.url, window.location.origin);
    const params = new URLSearchParams(new FormData(catalogForm));

    url.searchParams.set('length', '100');

    params.forEach((value, key) => {
        if (value !== '') {
            url.searchParams.set(key, value);
        }
    });

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
                    ${book.cover_url
                        ? `<img src="${book.cover_url}" alt="Sampul ${escapeHtml(book.title_text ?? '')}" class="mb-3 h-40 w-full rounded-md object-cover" loading="lazy">`
                        : `<div class="mb-3 flex h-40 w-full items-center justify-center rounded-md bg-slate-100 text-ink/40"><svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg></div>`}
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
