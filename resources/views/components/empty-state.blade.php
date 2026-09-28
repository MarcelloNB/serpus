@props(['message' => 'Belum ada data'])

<div {{ $attributes->merge(['class' => 'px-6 py-12 text-center']) }}>
    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-sand text-forest">
        <x-icon name="book" class="h-6 w-6" />
    </span>

    <p class="mt-3 text-sm font-semibold text-ink">{{ $message }}</p>
</div>
