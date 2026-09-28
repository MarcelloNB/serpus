<p align="center">
    <img src="public/images/serpus-logo.svg" width="120" alt="Logo SerPus">
</p>

# SerPus — Sistem Elektronik Perpustakaan

Aplikasi perpustakaan sederhana untuk mencatat peminjaman dan pengembalian buku:
peminjam mencari buku di katalog dan mengajukan peminjaman, admin mencatat
pengembalian serta mengelola data buku, kategori, dan pengguna.

Dibangun dengan **Laravel 13**, **PHP 8.5**, **MySQL**, **Blade**, dan **JavaScript**
(Tailwind CSS, Alpine.js, DataTables tanpa jQuery).

## Fitur

**Publik (tamu)**
- Landing page: hero carousel full-screen (slide sampul buku, CTA per peran), Buku Terpopuler
  (10 kartu berperingkat), berita kegiatan perpustakaan, dan buku terbaru.
- Katalog buku berbentuk kartu cover-first (grid 2–4 kolom, seluruh kartu dapat diklik)
  dengan **live search AJAX** tanpa reload halaman.
- Halaman detail buku bergaya pita editorial (sampul, judul, penulis, kategori, deskripsi, stok).

**Peminjam**
- Registrasi otomatis ber peran `peminjam`; profil dapat diperbarui.
- Meminjam buku dari halaman detail (stok berkurang, ada validasi stok habis).
- Riwayat peminjaman pribadi (read-only, pengembalian dicatat admin).
- Menu sidebar: Katalog Buku dan Riwayat Peminjaman.

**Admin**
- Dashboard: statistik pastel (total buku, sedang dipinjam, total pengguna) + peminjaman terbaru.
- CRUD Buku (termasuk **unggah sampul** JPG/PNG/WEBP maksimal 2 MB), Kategori, dan Pengguna dengan **DataTables server-side** (urut, cari, paginasi di server).
- Mencatat peminjaman manual dan **pengembalian** — stok buku dikembalikan dalam satu
  transaksi database dengan penguncian baris (`lockForUpdate`) agar tidak terjadi race condition.
- Menu sidebar: Dashboard, Buku, Kategori, Pengguna, Peminjaman.

**Otorisasi & validasi**
- Pembatasan akses lewat middleware, tanpa Gate/policy:
  - `admin` — hanya admin yang boleh membuka `/admin/*` (lainnya 403).
  - `non-admin` — admin **tidak** bisa membuka riwayat (`/loans*`); tamu dan peminjam tetap bisa.
  - Katalog (`/katalog`, `/books/*`) terbuka untuk semua peran; admin dapat melihatnya,
    tetapi tombol pinjam menjadi label "Khusus peminjam" dan `POST /borrow` oleh admin
    dibalik dengan alert error (bukan 403).
- Validasi server-side memakai Form Request di seluruh endpoint tulis.

## Kebutuhan

- PHP >= 8.5 dan Composer
- MySQL
- Node.js 22+ dan npm

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Isi kredensial database di `.env` (`DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`,
`DB_PASSWORD`), lalu:

```bash
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`.

> `php artisan migrate:fresh --seed` **menghapus seluruh data** — hanya untuk
> database pengembangan.

## Akun bawaan (hasil seeder)

| Peran     | Email               | Password |
|-----------|---------------------|----------|
| Admin     | `admin@serpus.test` | `password` |
| Peminjam  | `peminjam@serpus.test` | `password` |

Pengguna lain dapat mendaftar sendiri melalui halaman **Daftar** (otomatis peminjam).

## Struktur proyek

```
app/
├── Enums/                  UserRole, LoanStatus
├── Http/
│   ├── Controllers/        Catalog, Home, LoanHistory, Profile, Admin/*, Auth/*
│   ├── Middleware/         EnsureUserIsAdmin, EnsureUserIsNotAdmin
│   └── Requests/           Form Request untuk validasi server-side
├── Services/LoanService    Logika pinjam/buku stok (transaksi + lockForUpdate)
└── Support/DataTables      Pembungkus respons server-side processing DataTables

database/
├── migrations/             users(+role), categories, books, loans
└── seeders/                UserSeeder, CategorySeeder, BookSeeder

resources/
├── views/                  Blade: layouts, components, catalog, admin, auth, home
├── css/app.css             Tailwind + aturan khusus container DataTables
└── js/app.js               Alpine, inisialisasi DataTables, live search katalog, carousel landing

routes/
├── web.php                 Landing, katalog, riwayat, profil, /dashboard (redirect per peran)
├── admin.php               Grup /admin dengan middleware admin
└── auth.php                Rute autentikasi Breeze
```

### Skema database

- `users` — nama, email, password, `role` (`admin` | `peminjam`).
- `categories` — nama kategori buku.
- `books` — judul, penulis, deskripsi, sampul (`cover_image`), stok, `category_id` (FK ke `categories`).
- `loans` — `user_id`, `book_id`, status (`dipinjam` | `dikembalikan`),
  `borrowed_at`, `returned_at`.

## Menjalankan pengujian

Proyek memakai **Pest** (SQLite in-memory, terpisah dari MySQL development).

```bash
php artisan test --compact      # 146 test, 571 assertions
vendor/bin/pint --dirty         # format kode mengikuti Laravel Pint
npm run build                   # bundel aset produksi
```

## Teknologi

| Bagian    | Teknologi |
|-----------|-----------|
| Backend   | Laravel 13, PHP 8.5, MySQL |
| Autentikasi | Laravel Breeze (stack Blade) |
| Frontend  | Blade, Tailwind CSS 3 (+ plugin forms), Alpine.js |
| Tabel data| DataTables 3 (server-side processing, tanpa jQuery) |
| Pengujian | Pest, Laravel Dusk tidak digunakan |
| Build     | Vite |
