@php
    $news = [
        ['date' => '18 September 2026', 'title' => 'Diskusi Buku Tiap Sabtu Sepanjang Liburan', 'excerpt' => 'Kelas berbagi bacaan gratis di aula perpustakaan, terbuka untuk seluruh anggota.'],
        ['date' => '5 September 2026', 'title' => 'Koleksi Baru: 120 Judul Sains dan Teknologi', 'excerpt' => 'Rak sains diperbarui dengan rujukan terbaru untuk pelajar dan mahasiswa.'],
        ['date' => '28 Agustus 2026', 'title' => 'Literasi Digital untuk Siswa SMA', 'excerpt' => 'Workshop mencari sumber kredibel dan menghindari hoaks berlangsung dua sesi.'],
    ];
@endphp

<x-public-layout :flush="true">
    <section class="relative isolate flex min-h-[calc(100svh-4rem)] items-center overflow-hidden bg-forest" data-carousel>
        @foreach ($heroSlides as $index => $slide)
            <div
                data-slide
                class="absolute inset-0 bg-cover bg-center transition-opacity duration-700 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                @if ($slide['image'])
                    style="background-image: url('{{ $slide['image'] }}')"
                @else
                    style="background-image: {{ $slide['style'] }}"
                @endif
            ></div>
        @endforeach

        <div class="absolute inset-0 bg-gradient-to-r from-forest via-forest/85 to-forest/45"></div>

        <div class="relative mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <p class="flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em] text-white">
                <span class="h-px w-10 bg-brand"></span>
                Perpustakaan Digital
            </p>

            <h1 class="mt-5 max-w-3xl text-4xl font-semibold leading-[1.1] tracking-tight text-white sm:text-5xl lg:text-6xl">
                Temukan buku, pinjam, dan kembalikan —
                <span class="italic underline decoration-brand decoration-4 underline-offset-8">tanpa antre</span>
                dan tanpa formulir kertas.
            </h1>

            <p class="mt-6 max-w-2xl text-lg leading-relaxed text-paper/85">
                Katalog, pengajuan peminjaman, dan riwayat pinjaman terangkum dalam satu tempat.
                Cukup dari browser, kapan saja.
            </p>

            <div class="mt-9 flex flex-wrap gap-3">
                @guest
                    <a
                        href="{{ route('register') }}"
                        class="rounded-md bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand/90 focus:ring-2 focus:ring-brand"
                    >
                        Daftar Sekarang
                    </a>
                    <a
                        href="{{ route('catalog.index') }}"
                        class="rounded-md border border-paper/40 px-5 py-2.5 text-sm font-semibold text-paper hover:bg-white/10 focus:ring-2 focus:ring-brand"
                    >
                        Lihat Katalog
                    </a>
                @elseif (auth()->user()->isAdmin())
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="rounded-md bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand/90 focus:ring-2 focus:ring-brand"
                    >
                        Buka Dashboard
                    </a>
                @else
                    <a
                        href="{{ route('catalog.index') }}"
                        class="rounded-md bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand/90 focus:ring-2 focus:ring-brand"
                    >
                        Buka Katalog
                    </a>
                @endguest
            </div>
        </div>

        <button
            type="button"
            data-carousel-prev
            aria-label="Slide sebelumnya"
            class="absolute left-4 top-1/2 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-paper/30 text-paper transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-brand sm:flex"
        >
            ‹
        </button>

        <button
            type="button"
            data-carousel-next
            aria-label="Slide berikutnya"
            class="absolute right-4 top-1/2 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-paper/30 text-paper transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-brand sm:flex"
        >
            ›
        </button>

        <div class="absolute bottom-8 left-1/2 flex -translate-x-1/2 gap-2">
            @foreach ($heroSlides as $index => $slide)
                <button
                    type="button"
                    data-carousel-dot
                    aria-label="Slide {{ $index + 1 }}"
                    class="h-2 w-2 rounded-full transition {{ $index === 0 ? 'bg-paper' : 'bg-paper/40' }}"
                ></button>
            @endforeach
        </div>
    </section>

    <section class="mx-auto mt-20 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-5">
            <h2 class="font-display text-2xl font-semibold text-ink">Buku Terpopuler</h2>
            <span class="h-px flex-1 bg-forest/15"></span>
            <span class="text-xs font-semibold uppercase tracking-widest text-ink/50">Peminjaman terbanyak</span>
        </div>

        @if ($popularBooks->isEmpty())
            <x-empty-state
                message="Belum ada buku yang dipinjam."
                class="mt-8 rounded-lg border border-dashed border-forest/20"
            />
        @else
            <div class="mt-8 grid gap-x-6 gap-y-8 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ($popularBooks as $book)
                    <x-book-card :book="$book" :rank="$loop->index + 1" />
                @endforeach
            </div>
        @endif
    </section>

    <section class="mt-24 bg-sand/60 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-5">
                <h2 class="font-display text-2xl font-semibold text-ink">Berita Kegiatan Perpustakaan</h2>
                <span class="h-px flex-1 bg-forest/15"></span>
            </div>

            <div class="mt-10 grid gap-10 md:grid-cols-3">
                @foreach ($news as $item)
                    <article class="border-t border-forest/20 pt-5">
                        <p class="text-xs font-semibold uppercase tracking-widest text-forest">{{ $item['date'] }}</p>
                        <h3 class="font-display mt-2 text-lg font-semibold leading-snug text-ink">{{ $item['title'] }}</h3>
                        <p class="mt-2 text-sm text-ink/70">{{ $item['excerpt'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto mt-24 max-w-7xl px-4 pb-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-5">
            <h2 class="font-display text-2xl font-semibold text-ink">Buku Terbaru</h2>
            <span class="h-px flex-1 bg-forest/15"></span>

            <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-ink/70 hover:text-brand">
                Lihat semua <span aria-hidden="true">→</span>
            </a>
        </div>

        @if ($latestBooks->isEmpty())
            <x-empty-state
                message="Belum ada buku di katalog."
                class="mt-8 rounded-lg border border-dashed border-forest/20"
            />
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($latestBooks as $book)
                    <x-book-card :book="$book" />
                @endforeach
            </div>
        @endif
    </section>
</x-public-layout>
