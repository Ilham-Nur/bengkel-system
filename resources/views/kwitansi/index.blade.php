@extends('layout.app')

@section('title', 'Kwitansi')

@section('content')
@php
    $role = auth()->user()?->role ?? 'pelanggan';
@endphp

<h1 class="page-title">Kwitansi</h1>

<section class="panel">
    <div class="panel-head">
        <strong>List Invoice</strong>
        @if ($role === 'admin')
            <a href="{{ route('kwitansi.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Buat Invoice</a>
        @endif
    </div>

    <div class="filter-wrap">
        <form method="GET" action="{{ route('kwitansi.index') }}" class="search-inline">
            <div>
                <label for="q">Pencarian</label>
                <input class="input" id="q" name="q" value="{{ $filters['q'] }}" placeholder="No invoice, nama, plat, no wo">
            </div>
            <input type="hidden" name="start_date" value="{{ $filters['start_date'] }}">
            <input type="hidden" name="end_date" value="{{ $filters['end_date'] }}">
            <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">
            <div class="filter-actions-inline">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                <button class="btn btn-light" type="button" data-open-filter><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('kwitansi.index') }}" class="btn btn-light"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="filter-modal" data-filter-modal hidden>
        <div class="filter-modal-backdrop" data-close-filter></div>
        <div class="filter-modal-card">
            <div class="filter-modal-head">
                <strong>Filter Invoice</strong>
                <button type="button" class="btn btn-light" data-close-filter><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="GET" action="{{ route('kwitansi.index') }}" class="filter-grid">
                <input type="hidden" name="q" value="{{ $filters['q'] }}">
                <div>
                    <label for="start_date_modal">Dari Tanggal</label>
                    <input class="input" id="start_date_modal" name="start_date" type="date" value="{{ $filters['start_date'] }}">
                </div>
                <div>
                    <label for="end_date_modal">Sampai Tanggal</label>
                    <input class="input" id="end_date_modal" name="end_date" type="date" value="{{ $filters['end_date'] }}">
                </div>
                <div>
                    <label for="per_page_modal">Data / Halaman</label>
                    <select class="input" id="per_page_modal" name="per_page">
                        @foreach ([5, 10, 25] as $limit)
                            <option value="{{ $limit }}" @selected((int) $filters['per_page'] === $limit)>{{ $limit }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-actions">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-funnel"></i> Terapkan</button>
                    <a href="{{ route('kwitansi.index', ['q' => $filters['q']]) }}" class="btn btn-light">Reset Filter</a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-wrap desktop-only">
        <table>
            <thead>
                <tr>
                    <th>No Invoice</th>
                    <th>No WO</th>
                    <th>Nama Customer</th>
                    <th>Tanggal</th>
                    <th>Plat Nomor</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $item)
                    <tr>
                        <td>{{ $item->no_invoice }}</td>
                        <td>{{ $item->workOrder?->no_wo ?? '-' }}</td>
                        <td>{{ $item->customer_name }}</td>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->plat_nomor }}</td>
                        <td>
                            @if ($item->is_paid)
                                <span class="status done">Lunas</span>
                            @else
                                <span class="status draft">Belum Lunas</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($item->total_kwitansi, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('kwitansi.pdf', $item) }}" target="_blank" class="btn btn-light"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                            @if ($role === 'admin')
                                <form action="{{ route('kwitansi.toggle-paid', $item) }}" method="POST" class="inline-form" style="display:inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn {{ $item->is_paid ? 'btn-warning' : 'btn-success' }}" type="submit">
                                        <i class="bi bi-patch-check"></i>
                                        {{ $item->is_paid ? 'Jadikan Belum Lunas' : 'Set Lunas' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; color:#64748b;">Belum ada data invoice.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-list">
        @forelse ($rows as $item)
            <article class="info-card">
                <h4>{{ $item->no_invoice }}</h4>
                <div class="kv"><span class="key">No WO</span><span>{{ $item->workOrder?->no_wo ?? '-' }}</span></div>
                <div class="kv"><span class="key">Customer</span><strong>{{ $item->customer_name }}</strong></div>
                <div class="kv"><span class="key">Tanggal</span><span>{{ $item->tanggal }}</span></div>
                <div class="kv"><span class="key">Plat</span><span>{{ $item->plat_nomor }}</span></div>
                <div class="kv"><span class="key">Status</span><span class="status {{ $item->is_paid ? 'done' : 'draft' }}">{{ $item->is_paid ? 'Lunas' : 'Belum Lunas' }}</span></div>
                <div class="kv"><span class="key">Total</span><strong>Rp {{ number_format($item->total_kwitansi, 0, ',', '.') }}</strong></div>
                <div style="display:flex; gap:.45rem; flex-wrap:wrap; margin-top:.65rem;">
                    <a href="{{ route('kwitansi.pdf', $item) }}" target="_blank" class="btn btn-light"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                    @if ($role === 'admin')
                        <form action="{{ route('kwitansi.toggle-paid', $item) }}" method="POST" class="inline-form">
                            @csrf
                            @method('PATCH')
                            <button class="btn {{ $item->is_paid ? 'btn-warning' : 'btn-success' }}" type="submit">
                                <i class="bi bi-patch-check"></i>
                                {{ $item->is_paid ? 'Jadikan Belum Lunas' : 'Set Lunas' }}
                            </button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <article class="info-card" style="text-align:center; color:#64748b;">Belum ada data invoice.</article>
        @endforelse
    </div>

    <div class="pagination-wrap">
        {{ $rows->links() }}
    </div>
</section>
@endsection

@push('scripts')
<script>
    const modal = document.querySelector('[data-filter-modal]');
    document.querySelectorAll('[data-open-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            modal?.removeAttribute('hidden');
        });
    });
    document.querySelectorAll('[data-close-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            modal?.setAttribute('hidden', 'hidden');
        });
    });
</script>
<style>
    .filter-wrap { padding: 1rem; border-bottom: var(--border); }
    .search-inline { display:grid; gap:.8rem; grid-template-columns: 1fr; }
    .filter-actions-inline { display:flex; gap:.5rem; flex-wrap:wrap; align-items:end; }
    .filter-modal { position: fixed; inset: 0; z-index: 40; display:flex; align-items:center; justify-content:center; padding: 1rem; }
    .filter-modal[hidden] { display: none !important; }
    .filter-modal-backdrop { position:absolute; inset:0; background:rgba(15, 23, 42, .42); }
    .filter-modal-card { position:relative; width:min(560px, 100%); background:#fff; border-radius:14px; box-shadow: var(--shadow); padding:1rem; }
    .filter-modal-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:.8rem; }
    .filter-grid { display:grid; gap:.8rem; }
    .filter-actions { display:flex; gap:.5rem; flex-wrap:wrap; }
    .pagination-wrap { padding: .9rem 1rem 1rem; }
    @media (min-width: 768px) {
        .search-inline { grid-template-columns: minmax(0, 1fr) auto; align-items:end; }
    }
</style>
@endpush
