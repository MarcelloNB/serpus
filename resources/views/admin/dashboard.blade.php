<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-lg font-semibold">Dashboard Admin</h1>
    </x-slot>

    <div class="rounded-lg border border-slate-200 bg-white p-6">
        <h2 class="text-base font-semibold">Selamat datang, {{ auth()->user()->name }}</h2>
        <p class="mt-1 text-sm text-ink/70">
            Kelola data buku, kategori, dan pengguna, serta pantau peminjaman yang sedang berjalan melalui menu di samping.
        </p>
    </div>
</x-app-layout>
