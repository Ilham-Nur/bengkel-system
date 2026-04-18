@extends('layout.app')

@section('title', 'Work Order')

@section('content')
@php
    $role = auth()->user()?->role ?? 'pelanggan';
@endphp

<h1 class="page-title">Work Order</h1>
<div class="role-box">Role aktif: <strong>{{ ucfirst($role) }}</strong></div>

<section class="panel">
    <div class="panel-head">
        <strong>List Work Order</strong>
        @if ($role === 'admin')
            <a href="{{ route('workorder.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Work Order</a>
        @endif
    </div>

    <div class="filter-wrap">
        <form method="GET" action="{{ route('workorder.index') }}" class="search-inline">
            <div class="search-field">
                <label for="q">Pencarian</label>
                <input class="input" id="q" name="q" value="{{ $filters['q'] }}" placeholder="No WO, customer, plat, motor">
            </div>
            <input type="hidden" name="start_date" value="{{ $filters['start_date'] }}">
            <input type="hidden" name="end_date" value="{{ $filters['end_date'] }}">
            <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">
            <div class="filter-actions-inline">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                <button class="btn btn-light" type="button" data-open-filter><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('workorder.index') }}" class="btn btn-light"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </form>
    </div>

    <div class="filter-modal" data-filter-modal hidden>
        <div class="filter-modal-backdrop" data-close-filter></div>
        <div class="filter-modal-card">
            <div class="filter-modal-head">
                <strong>Filter Work Order</strong>
                <button type="button" class="btn btn-light" data-close-filter><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="GET" action="{{ route('workorder.index') }}" class="filter-grid">
                <input type="hidden" name="q" value="{{ $filters['q'] }}">
                <div>
                    <label for="start_date_modal">Dari Tanggal WO</label>
                    <input class="input" id="start_date_modal" name="start_date" type="date" value="{{ $filters['start_date'] }}">
                </div>
                <div>
                    <label for="end_date_modal">Sampai Tanggal WO</label>
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
                    <a href="{{ route('workorder.index', ['q' => $filters['q']]) }}" class="btn btn-light">Reset Filter</a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-wrap desktop-only">
        <table>
            <thead>
                <tr>
                    <th>No WO</th>
                    <th>Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Motor</th>
                    <th>Plat</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($workOrders as $item)
                    <tr>
                        <td>{{ $item->no_wo }}</td>
                        <td>{{ $item->customer?->name ?? '-' }}</td>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->jenis_motor }}</td>
                        <td>{{ $item->plat_nomor }}</td>
                        <td>Rp {{ number_format($item->total_keluhan_biaya, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('workorder.pdf', $item) }}" target="_blank" class="btn btn-light"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                            @if ($role === 'admin')
                                <a href="{{ route('workorder.edit', $item) }}" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                                <form action="{{ route('workorder.destroy', $item) }}" method="POST" class="inline-form form-delete" data-confirm-title="Hapus Work Order?" data-confirm-text="Data akan dihapus permanen.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash3"></i> Delete</button>
                                </form>
                            @else
                                <span class="status process">Tercatat</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; color:#64748b;">Belum ada data work order.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-list">
        @forelse ($workOrders as $item)
            <article class="info-card">
                <h4>{{ $item->no_wo }}</h4>
                <div class="kv"><span class="key">Pelanggan</span><strong>{{ $item->customer?->name ?? '-' }}</strong></div>
                <div class="kv"><span class="key">Tanggal</span><span>{{ $item->tanggal }}</span></div>
                <div class="kv"><span class="key">Motor</span><span>{{ $item->jenis_motor }}</span></div>
                <div class="kv"><span class="key">Plat</span><span>{{ $item->plat_nomor }}</span></div>
                <div class="kv"><span class="key">Total</span><strong>Rp {{ number_format($item->total_keluhan_biaya, 0, ',', '.') }}</strong></div>
                <div class="mobile-actions">
                    <a href="{{ route('workorder.pdf', $item) }}" target="_blank" class="btn btn-light"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                </div>
                @if ($role === 'admin')
                    <div class="mobile-actions">
                        <a href="{{ route('workorder.edit', $item) }}" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form action="{{ route('workorder.destroy', $item) }}" method="POST" class="inline-form form-delete" data-confirm-title="Hapus Work Order?" data-confirm-text="Data akan dihapus permanen.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"><i class="bi bi-trash3"></i> Delete</button>
                        </form>
                    </div>
                @endif
            </article>
        @empty
            <article class="info-card" style="text-align:center; color:#64748b;">Belum ada data work order.</article>
        @endforelse
    </div>

    <div class="pagination-wrap">
        {{ $workOrders->links() }}
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.form-delete').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            Swal.fire({
                title: form.dataset.confirmTitle || 'Yakin?',
                text: form.dataset.confirmText || 'Proses tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

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
    .inline-form { display:inline-flex; }
    .mobile-actions { display:flex; flex-wrap:wrap; gap:.45rem; margin-top:.65rem; }
    .filter-wrap { padding: 1rem; border-bottom: var(--border); }
    .search-inline { display:grid; gap:.8rem; grid-template-columns: 1fr; }
    .filter-actions-inline { display:flex; gap:.5rem; flex-wrap:wrap; align-items:end; }
    .search-field { min-width: 0; }
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
    @media (max-width: 767px) {
        .btn { width: 100%; justify-content: center; }
        .mobile-actions .inline-form { width: 100%; }
    }
</style>
@endpush
