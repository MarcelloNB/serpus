@use('App\Enums\UserRole')

<x-badge :variant="$role === UserRole::Admin ? 'brand' : 'slate'">
    {{ $role->label() }}
</x-badge>
