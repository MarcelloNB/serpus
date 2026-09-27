<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-lg font-semibold">Ubah Kategori</h1>
    </x-slot>

    <div class="max-w-xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="name" value="Nama Kategori" />
                <x-text-input
                    id="name"
                    name="name"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('name', $category->name)"
                    required
                    autofocus
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button>Simpan Perubahan</x-primary-button>
                <a href="{{ route('admin.categories.index') }}" class="text-sm font-medium text-ink hover:underline">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
