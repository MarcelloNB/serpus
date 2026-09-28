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
        pagingType: 'simple_numbers',
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
                next: '›',
                previous: '‹',
                last: 'Terakhir',
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
                <div class="flex flex-col rounded-lg border border-forest/10 bg-white p-4 transition hover:border-brand/60 hover:shadow-md">
                    ${book.cover_url
                        ? `<img src="${book.cover_url}" alt="Sampul ${escapeHtml(book.title_text ?? '')}" class="mb-3 h-40 w-full rounded-md object-cover shadow-sm" loading="lazy">`
                        : `<div class="mb-3 flex h-40 w-full flex-col justify-between rounded-md p-3" style="background-color:${book.spine_color}"><span class="h-1.5 w-10 rounded-full bg-paper/70"></span><span class="font-display text-4xl font-semibold text-paper">${escapeHtml((book.title_text ?? '').charAt(0).toUpperCase())}</span></div>`}
                    <h3 class="font-display text-sm font-semibold leading-snug">${book.title}</h3>
                    <p class="mt-1 text-xs text-ink/70">${escapeHtml(book.author)}</p>
                    <div class="mt-3 flex items-center justify-between gap-2 text-xs">
                        <span class="rounded-full px-2 py-0.5 font-medium text-ink" style="background-color:${book.spine_color}26">${escapeHtml(book.category_name ?? 'Tanpa kategori')}</span>
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
