@use('App\Enums\LoanStatus')

<x-badge :variant="$status === LoanStatus::Dipinjam ? 'amber' : 'emerald'">
    {{ $status->label() }}
</x-badge>
