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
                <a href="/books/${book.id}" class="group block rounded-lg focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 focus:ring-offset-paper">
                    <div class="relative aspect-[2/3] overflow-hidden rounded-lg shadow-md transition duration-200 group-hover:-translate-y-1.5 group-hover:shadow-xl" style="background-color:${book.spine_color}">
                        ${book.cover_url
                            ? `<img src="${book.cover_url}" alt="Sampul ${escapeHtml(book.title_text ?? '')}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">`
                            : `<div class="flex h-full w-full flex-col justify-between p-4" aria-hidden="true"><span class="h-1.5 w-12 rounded-full bg-paper/70"></span><span class="font-display text-6xl font-semibold text-white">${escapeHtml((book.title_text ?? '').charAt(0).toUpperCase())}</span></div>`}
                    </div>
                    <div class="mt-3">
                        <h3 class="font-display text-sm font-semibold leading-snug transition group-hover:text-brand">${escapeHtml(book.title_text ?? '')}</h3>
                        <p class="mt-1 text-xs text-ink/70">${escapeHtml(book.author)}</p>
                        <div class="mt-2.5 flex items-center justify-between gap-2 text-xs">
                            <span class="rounded-full px-2 py-0.5 font-medium text-ink" style="background-color:${book.spine_color}26">${escapeHtml(book.category_name ?? 'Tanpa kategori')}</span>
                            ${book.stock}
                        </div>
                    </div>
                </a>`,
        )
        .join('');
}

document.querySelectorAll('[data-carousel]').forEach((carousel) => {
    const slides = Array.from(carousel.querySelectorAll('[data-slide]'));
    const dots = Array.from(carousel.querySelectorAll('[data-carousel-dot]'));

    if (slides.length < 2) {
        return;
    }

    let current = 0;
    let timer;

    const show = (index) => {
        current = (index + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            slide.classList.toggle('opacity-100', i === current);
            slide.classList.toggle('opacity-0', i !== current);
        });

        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-paper', i === current);
            dot.classList.toggle('bg-paper/40', i !== current);
        });
    };

    const start = () => {
        timer = window.setInterval(() => show(current + 1), 5000);
    };

    const stop = () => window.clearInterval(timer);

    carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
        show(current + 1);
        stop();
        start();
    });

    carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
        show(current - 1);
        stop();
        start();
    });

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            show(i);
            stop();
            start();
        });
    });

    carousel.addEventListener('pointerenter', stop);
    carousel.addEventListener('pointerleave', start);

    start();
});

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}
