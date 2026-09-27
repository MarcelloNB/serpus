@auth
    <x-app-layout>
        <x-slot name="header">
            <h1 class="truncate text-lg font-semibold">Katalog Buku</h1>
        </x-slot>

        @include('catalog.table')
    </x-app-layout>
@else
    <x-public-layout>
        <h1 class="mb-4 text-lg font-semibold">Katalog Buku</h1>

        @include('catalog.table')
    </x-public-layout>
@endauth
