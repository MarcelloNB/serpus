@props(['url', 'columns', 'order' => []])

<div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto p-4">
        <table
            data-datatable
            data-url="{{ $url }}"
            data-columns="{{ json_encode($columns) }}"
            data-order="{{ json_encode($order) }}"
            class="w-full text-sm"
        >
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-ink/70">
                    @foreach ($columns as $column)
                        <th scope="col" class="px-4 py-3 text-start">{{ $column['title'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
