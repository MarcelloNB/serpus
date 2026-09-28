@auth
    <x-app-layout>
        <x-slot name="header">
            <h1 class="truncate text-xl font-semibold">Katalog Buku</h1>
        </x-slot>

        @include('catalog.cards')
    </x-app-layout>
@else
    <x-public-layout>
        <h1 class="mb-5 text-2xl font-semibold">Katalog Buku</h1>

        @include('catalog.cards')
    </x-public-layout>
@endauth
