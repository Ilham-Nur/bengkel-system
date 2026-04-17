@extends('layout.app')

@section('title', 'Form Work Order')

@section('content')
@php
    $mode = request('mode', 'create');
    $title = $mode === 'edit' ? 'Edit Work Order' : 'Tambah Work Order';
@endphp

<h1 class="page-title">{{ $title }}</h1>
<section class="form-page">
    <form>
        <div class="form-grid">
            <div>
                <label for="customer">Nama Customer</label>
                <select id="customer" class="input">
                    <option>Budi Santoso</option>
                    <option>Rina Wijaya</option>
                    <option>Andi Pratama</option>
                </select>
            </div>
            <div>
                <label for="hp">No HP (auto dari pelanggan)</label>
                <input class="input" id="hp" value="0812-3456-7890" readonly>
            </div>
            <div>
                <label for="tanggal">Tanggal Check</label>
                <input class="input" id="tanggal" type="date" value="2026-04-17">
            </div>
            <div>
                <label for="jenis">Jenis Motor</label>
                <input class="input" id="jenis" placeholder="Contoh: Honda Vario 160">
            </div>
            <div>
                <label for="plat">Plat Nomor</label>
                <input class="input" id="plat" placeholder="Contoh: B 1234 XYZ">
            </div>
            <div>
                <label for="km">KM Motor</label>
                <input class="input" id="km" type="number" placeholder="Contoh: 12000">
            </div>
            <div class="full">
                <label for="keluhan">Keluhan</label>
                <textarea id="keluhan" placeholder="Tuliskan keluhan customer..."></textarea>
            </div>
        </div>

        <div class="form-action">
            <button type="button" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
            <a href="{{ route('workorder.index') }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </form>
</section>
@endsection
