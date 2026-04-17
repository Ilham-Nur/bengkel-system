@extends('layout.app')

@section('title', 'Laporan Pekerjaan')

@section('content')
@php
    $role = auth()->user()?->role ?? 'pelanggan';
    $rows = [
        ['service' => 'SV-2026-0101', 'nama' => 'Budi Santoso', 'plat' => 'B 1234 XYZ', 'jenis' => 'Honda Vario', 'km' => '12.000'],
        ['service' => 'SV-2026-0102', 'nama' => 'Rina Wijaya', 'plat' => 'D 8888 KM', 'jenis' => 'Yamaha NMAX', 'km' => '18.500'],
        ['service' => 'SV-2026-0103', 'nama' => 'Andi Pratama', 'plat' => 'F 7771 AQ', 'jenis' => 'Beat Street', 'km' => '7.350'],
    ];
@endphp

<h1 class="page-title">Laporan Pekerjaan</h1>

<section class="panel">
    <div class="panel-head">
        <strong>Data Laporan</strong>
    </div>

    <div class="table-wrap desktop-only">
        <table>
            <thead>
                <tr>
                    <th>No Service</th>
                    <th>Nama Customer</th>
                    <th>Plat Nomor</th>
                    <th>Jenis Motor</th>
                    <th>KM Motor</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $item)
                    <tr>
                        <td>{{ $item['service'] }}</td>
                        <td>{{ $item['nama'] }}</td>
                        <td>{{ $item['plat'] }}</td>
                        <td>{{ $item['jenis'] }}</td>
                        <td>{{ $item['km'] }}</td>
                        <td>
                            <a href="#" class="btn btn-light"><i class="bi bi-eye"></i> Detail</a>
                            @if ($role === 'admin')
                                <a href="#" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                                <a href="{{ route('kwitansi.index') }}" class="btn btn-primary"><i class="bi bi-arrow-repeat"></i> Convert</a>
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
                <h4>{{ $item['service'] }}</h4>
                <div class="kv"><span class="key">Customer</span><strong>{{ $item['nama'] }}</strong></div>
                <div class="kv"><span class="key">Plat</span><span>{{ $item['plat'] }}</span></div>
                <div class="kv"><span class="key">Jenis</span><span>{{ $item['jenis'] }}</span></div>
                <div class="kv"><span class="key">KM</span><span>{{ $item['km'] }}</span></div>
                <div style="display:flex; gap:.4rem; flex-wrap:wrap; margin-top:.6rem;">
                    <a href="#" class="btn btn-light"><i class="bi bi-eye"></i> Detail</a>
                    @if ($role === 'admin')
                        <a href="#" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                        <a href="{{ route('kwitansi.index') }}" class="btn btn-primary"><i class="bi bi-arrow-repeat"></i> Convert</a>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
</section>
@endsection
