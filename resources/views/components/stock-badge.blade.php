@php($stock = (int) $stock)

<span
    @class([
        'inline-flex rounded-full px-2 py-0.5 text-xs font-semibold',
        'bg-red-100 text-red-800' => $stock < 1,
        'bg-amber-100 text-amber-800' => $stock >= 1 && $stock <= 3,
        'bg-emerald-100 text-emerald-800' => $stock > 3,
    ])
>
    @if ($stock < 1)
        Stok habis
    @elseif ($stock <= 3)
        Stok menipis
    @else
        Tersedia
    @endif
</span>
