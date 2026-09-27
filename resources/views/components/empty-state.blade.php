@props(['message' => 'Belum ada data'])

<div {{ $attributes->merge(['class' => 'px-6 py-12 text-center']) }}>
    <p class="text-sm font-semibold text-ink">{{ $message }}</p>
</div>
