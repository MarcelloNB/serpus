@use('App\Enums\UserRole')

<div>
    <x-input-label for="name" value="Nama" />
    <x-text-input
        id="name"
        name="name"
        type="text"
        class="mt-1 block w-full"
        :value="old('name', $user?->name)"
        required
        autofocus
    />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="email" value="Email" />
    <x-text-input
        id="email"
        name="email"
        type="email"
        class="mt-1 block w-full"
        :value="old('email', $user?->email)"
        required
    />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<div>
    <x-input-label for="password" value="Password" />
    <x-text-input
        id="password"
        name="password"
        type="password"
        class="mt-1 block w-full"
        @if (! $user?->exists) required @endif
    />
    @if ($user?->exists)
        <p class="mt-1 text-xs text-ink/60">Kosongkan kolom ini jika tidak ingin mengganti password.</p>
    @endif
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

<div>
    <x-input-label for="role" value="Peran" />
    <select
        id="role"
        name="role"
        class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
        required
    >
        @foreach ($roles as $role)
            <option
                value="{{ $role->value }}"
                @selected((string) old('role', $user?->role?->value ?? UserRole::Peminjam->value) === $role->value)
            >
                {{ $role->label() }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('role')" class="mt-2" />
</div>
