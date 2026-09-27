<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-lg font-semibold">Ubah Buku</h1>
    </x-slot>

    <div class="max-w-2xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.books.update', $book) }}" class="space-y-4">
            @csrf
            @method('PUT')

            @include('admin.books.fields')

            <div class="flex items-center gap-3">
                <x-primary-button>Simpan Perubahan</x-primary-button>
                <a href="{{ route('admin.books.index') }}" class="text-sm font-medium text-ink hover:underline">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
