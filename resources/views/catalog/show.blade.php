@auth
    <x-app-layout>
        <x-slot name="header">
            <h1 class="truncate text-lg font-semibold">Detail Buku</h1>
        </x-slot>

        @include('catalog.detail')
    </x-app-layout>
@else
    <x-public-layout>
        <h1 class="mb-4 text-lg font-semibold">Detail Buku</h1>

        @include('catalog.detail')
    </x-public-layout>
@endauth
