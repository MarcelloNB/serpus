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
    <x-input-label for="password" value="Kata sandi" />
    <x-password-input
        id="password"
        name="password"
        class="mt-1 block w-full"
        :required="! $user?->exists"
        generate
    />
    @if ($user?->exists)
        <p class="mt-1 text-xs text-ink/60">Kosongkan kolom ini jika tidak ingin mengganti password.</p>
    @else
        <p class="mt-1 text-xs text-ink/60">Minimal 8 karakter.</p>
    @endif
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

<div>
    <x-input-label for="role" value="Peran" />
    <select
        id="role"
        name="role"
        class="mt-1 block w-full rounded-md"
        :class="$errors->has('role') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-slate-300 focus:border-brand focus:ring-brand'"
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
