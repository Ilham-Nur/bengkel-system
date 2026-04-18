<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class KwitansiController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->string('q', ''));
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = (int) $request->input('per_page', 10);

        if (! in_array($perPage, [5, 10, 25], true)) {
            $perPage = 10;
        }

        $rows = collect([
            ['invoice' => 'INV-2026-0012', 'nama' => 'Budi Santoso', 'plat' => 'B 1234 XYZ', 'tanggal' => '2026-04-04'],
            ['invoice' => 'INV-2026-0013', 'nama' => 'Rina Wijaya', 'plat' => 'D 8888 KM', 'tanggal' => '2026-04-09'],
            ['invoice' => 'INV-2026-0014', 'nama' => 'Andi Pratama', 'plat' => 'F 7771 AQ', 'tanggal' => '2026-04-12'],
            ['invoice' => 'INV-2026-0015', 'nama' => 'Sari Ananda', 'plat' => 'B 4472 CK', 'tanggal' => '2026-04-15'],
            ['invoice' => 'INV-2026-0016', 'nama' => 'Rudi Hartono', 'plat' => 'E 1620 PJ', 'tanggal' => '2026-04-16'],
        ]);

        $filteredRows = $rows
            ->when($query !== '', function (Collection $collection) use ($query): Collection {
                return $collection->filter(function (array $row) use ($query): bool {
                    return str_contains(strtolower($row['invoice']), strtolower($query))
                        || str_contains(strtolower($row['nama']), strtolower($query))
                        || str_contains(strtolower($row['plat']), strtolower($query));
                });
            })
            ->when(filled($startDate), fn (Collection $collection) => $collection->filter(fn (array $row): bool => $row['tanggal'] >= $startDate))
            ->when(filled($endDate), fn (Collection $collection) => $collection->filter(fn (array $row): bool => $row['tanggal'] <= $endDate))
            ->sortByDesc('tanggal')
            ->values();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $filteredRows->forPage($currentPage, $perPage)->values();

        $paginatedRows = new LengthAwarePaginator(
            items: $currentItems,
            total: $filteredRows->count(),
            perPage: $perPage,
            currentPage: $currentPage,
            options: ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('kwitansi.index', [
            'rows' => $paginatedRows,
            'filters' => [
                'q' => $query,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'per_page' => $perPage,
            ],
        ]);
    }
}
