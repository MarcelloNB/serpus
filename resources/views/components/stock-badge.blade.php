@php($stock = (int) $stock)

<x-badge :variant="$stock < 1 ? 'red' : ($stock <= 3 ? 'amber' : 'emerald')">
    @if ($stock < 1)
        Stok habis
    @elseif ($stock <= 3)
        Stok menipis
    @else
        Tersedia
    @endif
</x-badge>
