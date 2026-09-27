<x-app-layout>
    <x-slot name="header">
        <h1 class="truncate text-lg font-semibold">Dashboard Admin</h1>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-sky-200 bg-sky-100 p-6">
                <p class="text-sm font-medium text-sky-700">Total Buku</p>
                <p class="mt-1 text-2xl font-semibold text-sky-900">{{ $totalBooks }}</p>
            </div>

            <div class="rounded-lg border border-emerald-200 bg-emerald-100 p-6">
                <p class="text-sm font-medium text-emerald-700">Sedang Dipinjam</p>
                <p class="mt-1 text-2xl font-semibold text-emerald-900">{{ $activeLoans }}</p>
            </div>

            <div class="rounded-lg border border-amber-200 bg-amber-100 p-6">
                <p class="text-sm font-medium text-amber-700">Total Pengguna</p>
                <p class="mt-1 text-2xl font-semibold text-amber-900">{{ $totalUsers }}</p>
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-base font-semibold">Peminjaman Terbaru</h2>
            </div>

            @if ($latestLoans->isEmpty())
                <x-empty-state message="Belum ada peminjaman." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs uppercase text-ink/70">
                                <th class="px-6 py-3 font-medium">Peminjam</th>
                                <th class="px-6 py-3 font-medium">Buku</th>
                                <th class="px-6 py-3 font-medium">Tanggal Pinjam</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($latestLoans as $loan)
                                <tr>
                                    <td class="px-6 py-3">{{ $loan->user->name }}</td>
                                    <td class="px-6 py-3">{{ $loan->book->title }}</td>
                                    <td class="px-6 py-3">{{ $loan->borrowed_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-3">
                                        <x-loan-status-badge :status="$loan->status" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
