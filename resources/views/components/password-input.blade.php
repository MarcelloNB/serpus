@props(['id', 'name', 'autocomplete' => 'new-password', 'required' => false, 'generate' => false])

@php
    $invalid = filled($name) && $errors->has($name);
@endphp

<div
    x-data="{
        show: false,
        generate() {
            const characters = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            const bytes = new Uint32Array(12);
            crypto.getRandomValues(bytes);
            this.$refs.field.value = Array.from(bytes, (byte) => characters[byte % characters.length]).join('');
        },
    }"
    class="relative"
>
    <input
        x-ref="field"
        :type="show ? 'text' : 'password'"
        id="{{ $id }}"
        name="{{ $name }}"
        autocomplete="{{ $autocomplete }}"
        @required($required)
        {{ $attributes->merge(['class' => ($invalid ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-slate-300 focus:border-brand focus:ring-brand').' rounded-md shadow-sm '.($generate ? 'pr-28' : 'pr-10')]) }}
    >
    <div class="absolute inset-y-0 right-0 flex items-center gap-1 pr-2">
        @if ($generate)
            <button
                type="button"
                x-on:click="generate()"
                class="rounded px-2 py-1 text-xs font-semibold text-ink/60 hover:text-ink"
            >
                Buatkan
            </button>
        @endif
        <button
            type="button"
            x-on:click="show = ! show"
            :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
            aria-label="Tampilkan kata sandi"
            class="p-1 text-ink/50 hover:text-ink"
        >
            <svg
                x-show="! show"
                x-cloak
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <svg
                x-show="show"
                x-cloak
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.774 3.162 10.066 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243"
                />
            </svg>
        </button>
    </div>
</div>
