<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-lg font-semibold">Tambah Pengguna</h1>
    </x-slot>

    <div class="max-w-xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            @include('admin.users.fields')

            <div class="flex items-center gap-3">
                <x-primary-button>Simpan</x-primary-button>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-ink hover:underline">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
