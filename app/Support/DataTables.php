<?php

namespace App\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DataTables
{
    /**
     * Batas terpanjang halaman data yang mau diproses server.
     */
    public const MAX_LENGTH = 100;

    /**
     * Susun respons server-side processing DataTables dari query yang diberikan.
     *
     * @param  array<string, string>  $columns  pemetaan key kolom di request ke nama kolom query
     * @param  null|callable(array<string, mixed>): array<string, mixed>  $each  penambah key turunan per baris
     * @return array{draw: int, recordsTotal: int, recordsFiltered: int, data: list<array<string, mixed>>}
     */
    public static function make(Builder $query, Request $request, array $columns, ?Closure $each = null): array
    {
        $draw = max(1, (int) $request->input('draw', 1));
        $start = max(0, (int) $request->input('start', 0));
        $length = min(self::MAX_LENGTH, max(1, (int) $request->input('length', 10)));

        $total = (clone $query)->count();

        self::search($query, $request, $columns);
        self::order($query, $request, $columns);

        $filtered = (clone $query)->count();

        $data = $query->offset($start)->limit($length)->get()->toArray();

        if ($each !== null) {
            $data = array_map($each, $data);
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ];
    }

    /**
     * Saring query dengan pencarian global maupun pencarian per kolom.
     *
     * @param  array<string, string>  $columns
     */
    private static function search(Builder $query, Request $request, array $columns): void
    {
        $keyword = trim((string) $request->input('search.value'));

        if ($keyword !== '') {
            $query->where(function (Builder $group) use ($columns, $keyword) {
                foreach (array_values($columns) as $column) {
                    $group->orWhere($column, 'like', '%'.$keyword.'%');
                }
            });
        }

        foreach ((array) $request->input('columns', []) as $columnRequest) {
            $columnRequest = (array) $columnRequest;
            $name = is_string($columnRequest['data'] ?? null) ? ($columns[$columnRequest['data']] ?? null) : null;
            $value = trim((string) ($columnRequest['search']['value'] ?? ''));

            if ($name !== null && $value !== '') {
                $query->where($name, 'like', '%'.$value.'%');
            }
        }
    }

    /**
     * Urutkan query sesuai kolom pertama yang diminta klien.
     *
     * @param  array<string, string>  $columns
     */
    private static function order(Builder $query, Request $request, array $columns): void
    {
        $requestColumns = (array) $request->input('columns', []);
        $orders = [];

        foreach ((array) $request->input('order', []) as $order) {
            $order = (array) $order;
            $requestColumn = (array) ($requestColumns[(int) ($order['column'] ?? 0)] ?? []);
            $key = $requestColumn['data'] ?? null;
            $name = is_string($key) ? ($columns[$key] ?? null) : null;

            if ($name !== null) {
                $orders[] = [$name, ($order['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc'];
            }
        }

        if ($orders === []) {
            return;
        }

        $query->reorder();

        foreach ($orders as [$column, $direction]) {
            $query->orderBy($column, $direction);
        }
    }
}
