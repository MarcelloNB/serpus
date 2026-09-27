<div>
    <x-input-label for="title" value="Judul" />
    <x-text-input
        id="title"
        name="title"
        type="text"
        class="mt-1 block w-full"
        :value="old('title', $book?->title)"
        required
        autofocus
    />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div>
    <x-input-label for="author" value="Penulis" />
    <x-text-input
        id="author"
        name="author"
        type="text"
        class="mt-1 block w-full"
        :value="old('author', $book?->author)"
        required
    />
    <x-input-error :messages="$errors->get('author')" class="mt-2" />
</div>

<div>
    <x-input-label for="category_id" value="Kategori" />
        <select
            id="category_id"
            name="category_id"
            class="mt-1 block w-full rounded-md"
            :class="$errors->has('category_id') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-slate-300 focus:border-brand focus:ring-brand'"
            required
        >
        <option value="">Pilih kategori</option>
        @foreach ($categories as $category)
            <option
                value="{{ $category->id }}"
                @selected((string) old('category_id', $book?->category_id) === (string) $category->id)
            >
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="stock" value="Stok" />
    <x-text-input
        id="stock"
        name="stock"
        type="number"
        min="0"
        class="mt-1 block w-full"
        :value="old('stock', $book?->stock ?? 0)"
        required
    />
    <x-input-error :messages="$errors->get('stock')" class="mt-2" />
</div>

<div>
    <x-input-label for="description" value="Deskripsi" />
    <textarea
        id="description"
        name="description"
        rows="4"
        class="mt-1 block w-full rounded-md"
        :class="$errors->has('description') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-slate-300 focus:border-brand focus:ring-brand'"
    >{{ old('description', $book?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>
