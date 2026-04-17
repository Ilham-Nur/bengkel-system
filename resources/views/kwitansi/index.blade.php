@extends('layout.app')

@section('title', 'Kwitansi')

@section('content')
@php
    $role = request('role', 'admin');
    $rows = [
        ['invoice' => 'INV-2026-0012', 'nama' => 'Budi Santoso', 'plat' => 'B 1234 XYZ'],
        ['invoice' => 'INV-2026-0013', 'nama' => 'Rina Wijaya', 'plat' => 'D 8888 KM'],
        ['invoice' => 'INV-2026-0014', 'nama' => 'Andi Pratama', 'plat' => 'F 7771 AQ'],
    ];
@endphp

<h1 class="page-title">Kwitansi</h1>

<section class="panel">
    <div class="panel-head">
        <strong>List Invoice</strong>
        @if ($role === 'admin')
            <button class="btn btn-primary"><i class="bi bi-plus-circle"></i> Buat Invoice</button>
        @endif
    </div>

    <div class="table-wrap desktop-only">
        <table>
            <thead>
                <tr>
                    <th>No Invoice</th>
                    <th>Nama Customer</th>
                    <th>Plat Nomor</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $item)
                    <tr>
                        <td>{{ $item['invoice'] }}</td>
                        <td>{{ $item['nama'] }}</td>
                        <td>{{ $item['plat'] }}</td>
                        <td>
                            <button class="btn btn-light"><i class="bi bi-eye"></i> Detail</button>
                            @if ($role === 'admin')
                                <button class="btn btn-light"><i class="bi bi-printer"></i> Print</button>
                                <button class="btn btn-success btn-lunas"><i class="bi bi-patch-check"></i> Stamp Lunas</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-list">
        @foreach ($rows as $item)
            <article class="info-card">
                <h4>{{ $item['invoice'] }}</h4>
                <div class="kv"><span class="key">Customer</span><strong>{{ $item['nama'] }}</strong></div>
                <div class="kv"><span class="key">Plat</span><span>{{ $item['plat'] }}</span></div>
                <div style="display:flex; gap:.4rem; flex-wrap:wrap; margin-top:.6rem;">
                    <button class="btn btn-light"><i class="bi bi-eye"></i> Detail</button>
                    @if ($role === 'admin')
                        <button class="btn btn-light"><i class="bi bi-printer"></i> Print</button>
                        <button class="btn btn-success btn-lunas"><i class="bi bi-patch-check"></i> Stamp Lunas</button>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-lunas').forEach((button) => {
        button.addEventListener('click', () => {
            Swal.fire('Berhasil', 'Invoice berhasil distamp lunas.', 'success');
        });
    });
</script>
@endpush
