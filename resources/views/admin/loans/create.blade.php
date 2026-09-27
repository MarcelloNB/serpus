<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-lg font-semibold">Tambah Peminjaman</h1>
    </x-slot>

    <div class="max-w-xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.loans.store') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="user_id" value="Peminjam" />
                <select
                    id="user_id"
                    name="user_id"
                    class="mt-1 block w-full rounded-md"
                    :class="$errors->has('user_id') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-slate-300 focus:border-brand focus:ring-brand'"
                    required
                >
                    <option value="">Pilih peminjam</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="book_id" value="Buku" />
                <select
                    id="book_id"
                    name="book_id"
                    class="mt-1 block w-full rounded-md"
                    :class="$errors->has('book_id') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-slate-300 focus:border-brand focus:ring-brand'"
                    required
                >
                    <option value="">Pilih buku</option>
                    @foreach ($books as $book)
                        <option value="{{ $book->id }}" @selected(old('book_id') == $book->id)>
                            {{ $book->title }} — stok {{ $book->stock }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('book_id')" class="mt-2" />
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button>Simpan</x-primary-button>
                <a href="{{ route('admin.loans.index') }}" class="text-sm font-medium text-ink hover:underline">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
