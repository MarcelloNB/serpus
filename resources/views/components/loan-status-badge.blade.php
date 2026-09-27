@use('App\Enums\LoanStatus')

<span
    @class([
        'inline-flex rounded-full px-2 py-0.5 text-xs font-semibold',
        'bg-amber-100 text-amber-800' => $status === LoanStatus::Dipinjam,
        'bg-emerald-100 text-emerald-800' => $status === LoanStatus::Dikembalikan,
    ])
>
    {{ $status->label() }}
</span>
