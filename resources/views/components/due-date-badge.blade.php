<span class="inline-flex items-center gap-1.5 whitespace-nowrap">
    <span>{{ $label }}</span>
    @if ($overdue)
        <x-badge variant="red">Terlambat</x-badge>
    @endif
</span>
