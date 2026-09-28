<x-public-layout>
    <section class="rounded-2xl border border-forest/15 bg-sand px-6 py-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-widest text-brand">Perpustakaan Digital</p>

        <h1 class="mx-auto mt-4 max-w-2xl text-4xl font-semibold text-forest sm:text-5xl">
            SerPus
        </h1>
        <h2 class="mx-auto mt-3 max-w-2xl text-xl font-medium text-ink/70 sm:text-2xl">
            Sistem Elektronik Perpustakaan
        </h2>

        <p class="mx-auto mt-5 max-w-2xl text-ink/70">
            Telusuri katalog, ajukan peminjaman, dan pantau riwayat pinjaman dalam satu tempat.
            Tanpa antre, tanpa formulir kertas.
        </p>

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            @guest
                <a
                    href="{{ route('register') }}"
                    class="rounded-md bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forest/90 focus:ring-2 focus:ring-brand"
                >
                    Daftar Sekarang
                </a>
                <a
                    href="{{ route('catalog.index') }}"
                    class="rounded-md border border-ink/20 bg-paper px-5 py-2.5 text-sm font-semibold hover:bg-white focus:ring-2 focus:ring-brand"
                >
                    Lihat Katalog
                </a>
            @elseif (auth()->user()->isAdmin())
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="rounded-md bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forest/90 focus:ring-2 focus:ring-brand"
                >
                    Buka Dashboard
                </a>
            @else
                <a
                    href="{{ route('catalog.index') }}"
                    class="rounded-md bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forest/90 focus:ring-2 focus:ring-brand"
                >
                    Buka Katalog
                </a>
            @endguest
        </div>
    </section>

    <section class="mt-10 grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-moss/30 bg-moss/10 p-6 text-center">
            <p class="font-display text-3xl font-semibold text-moss">{{ $totalBooks }}</p>
            <p class="mt-1 text-sm font-medium text-ink/70">Judul Buku</p>
        </div>

        <div class="rounded-lg border border-berry/30 bg-berry/10 p-6 text-center">
            <p class="font-display text-3xl font-semibold text-berry">{{ $totalCategories }}</p>
            <p class="mt-1 text-sm font-medium text-ink/70">Kategori</p>
        </div>

        <div class="rounded-lg border border-plum/30 bg-plum/10 p-6 text-center">
            <p class="font-display text-3xl font-semibold text-plum">{{ $totalUsers }}</p>
            <p class="mt-1 text-sm font-medium text-ink/70">Anggota Terdaftar</p>
        </div>
    </section>

    <section class="mt-10">
        <h2 class="text-lg font-semibold">Cara Meminjam Buku</h2>

        <ol class="mt-4 grid gap-4 md:grid-cols-3">
            <li class="rounded-lg border border-forest/10 bg-white p-6">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand text-sm font-bold text-white">1</span>
                <h3 class="mt-4 text-sm font-semibold">Daftar Akun</h3>
                <p class="mt-1 text-sm text-ink/70">Buat akun peminjam menggunakan email aktifmu.</p>
            </li>

            <li class="rounded-lg border border-forest/10 bg-white p-6">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand text-sm font-bold text-white">2</span>
                <h3 class="mt-4 text-sm font-semibold">Pilih Buku</h3>
                <p class="mt-1 text-sm text-ink/70">Telusuri katalog dan gunakan pencarian untuk menemukan buku yang kamu butuhkan.</p>
            </li>

            <li class="rounded-lg border border-forest/10 bg-white p-6">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand text-sm font-bold text-white">3</span>
                <h3 class="mt-4 text-sm font-semibold">Pinjam dan Kembalikan</h3>
                <p class="mt-1 text-sm text-ink/70">Ajukan peminjaman dari halaman detail buku, lalu kembalikan bukunya ke perpustakaan.</p>
            </li>
        </ol>
    </section>

    <section class="mt-10">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Buku Terbaru</h2>

            <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-ink/70 hover:text-brand">
                Lihat semua â†’
            </a>
        </div>

        @if ($latestBooks->isEmpty())
            <x-empty-state
                message="Belum ada buku di katalog."
                class="mt-4 rounded-lg border border-dashed border-forest/20"
            />
        @else
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($latestBooks as $book)
                    <x-book-card :book="$book" />
                @endforeach
            </div>
        @endif
    </section>
</x-public-layout>
