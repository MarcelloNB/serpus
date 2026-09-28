@props(['variant' => 'slate'])

<span
    @class([
        'inline-flex rounded-full px-2 py-0.5 text-xs font-semibold',
        'bg-emerald-100 text-emerald-800' => $variant === 'emerald',
        'bg-amber-100 text-amber-800' => $variant === 'amber',
        'bg-red-100 text-red-800' => $variant === 'red',
        'bg-brand text-white' => $variant === 'brand',
        'bg-slate-100 text-slate-700' => $variant === 'slate',
    ])
>
    {{ $slot }}
</span>
