<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Laporan {{ $workOrder->no_wo }}</title>
    <style>
        :root { color-scheme: light; }
        body { font-family: Arial, Helvetica, sans-serif; margin: 16px; color: #0f172a; }
        .toolbar { margin-bottom: 12px; display: flex; gap: 8px; }
        .btn { border: 0; border-radius: 8px; background: #0f766e; color: #fff; padding: 8px 12px; cursor: pointer; text-decoration: none; font-size: 13px; }
        .btn-light { background: #cbd5e1; color: #0f172a; }
        .card { border: 1px solid #cbd5e1; border-radius: 12px; padding: 14px; margin-bottom: 12px; }
        .meta { display: grid; grid-template-columns: repeat(2, minmax(180px, 1fr)); gap: 10px; }
        h1, h2, h3, h4 { margin: 0 0 8px; }
        .muted { color: #475569; font-size: 13px; }
        .complaint-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px; margin-bottom: 10px; }
        .kv { margin-bottom: 6px; font-size: 14px; }
        .kv .key { display: inline-block; min-width: 140px; color: #64748b; }
        .photo-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(135px, 1fr)); gap: 8px; margin-top: 6px; }
        .photo-card { border: 1px solid #cbd5e1; border-radius: 8px; padding: 4px; margin: 0; }
        .photo-card img { width: 100%; height: 120px; object-fit: cover; border-radius: 6px; }
        .status { display: inline-block; border-radius: 999px; padding: 3px 8px; font-size: 12px; background: #f1f5f9; }
        .status.done { background: #dcfce7; color: #14532d; }

        @media print {
            body { margin: 0; }
            .toolbar { display: none; }
            .card, .complaint-card { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn" onclick="window.print()">Export / Simpan PDF</button>
        <a class="btn btn-light" href="{{ route('laporan.show', $workOrder) }}">Kembali</a>
    </div>

    <section class="card">
        <h1>Laporan Pekerjaan {{ $workOrder->no_wo }}</h1>
        <div class="muted">Dokumen siap cetak PDF.</div>
        <div class="meta" style="margin-top:10px;">
            <div><strong>Pelanggan:</strong><br>{{ $workOrder->customer?->name ?? '-' }}</div>
            <div><strong>Tanggal WO:</strong><br>{{ $workOrder->tanggal }}</div>
            <div><strong>Jenis Motor:</strong><br>{{ $workOrder->jenis_motor }}</div>
            <div><strong>Plat Nomor:</strong><br>{{ $workOrder->plat_nomor }}</div>
        </div>
    </section>

    <section class="card">
        <h2>Detail Per Keluhan</h2>

        @foreach ($workOrder->complaintItems as $index => $complaint)
            @php
                $reportItem = $report?->items?->firstWhere('work_order_complaint_item_id', $complaint->id);
            @endphp
            <article class="complaint-card">
                <h3>Keluhan #{{ $index + 1 }}</h3>
                <div class="kv"><span class="key">Keluhan</span><strong>{{ $complaint->keluhan_item }}</strong></div>
                <div class="kv"><span class="key">Rekomendasi</span>{{ $complaint->rekomendasi_perbaikan ?: '-' }}</div>

                <h4>Foto Keluhan</h4>
                <div class="photo-grid">
                    @forelse ($complaint->photos as $photo)
                        <figure class="photo-card">
                            <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto keluhan">
                        </figure>
                    @empty
                        <small class="muted">Belum ada foto keluhan.</small>
                    @endforelse
                </div>

                <h4 style="margin-top:8px;">Hasil Service</h4>
                @if ($reportItem)
                    <span class="status done">Selesai</span>
                    <div class="kv" style="margin-top:6px;"><span class="key">Selesai Pada</span>{{ optional($reportItem->service_finished_at)->format('d-m-Y H:i') ?: '-' }}</div>
                    <div class="kv"><span class="key">Deskripsi Hasil</span>{{ $reportItem->service_description ?: '-' }}</div>

                    <h4>Foto Hasil Service</h4>
                    <div class="photo-grid">
                        @forelse ($reportItem->photos as $photo)
                            <figure class="photo-card">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto hasil service">
                            </figure>
                        @empty
                            <small class="muted">Belum ada foto hasil service.</small>
                        @endforelse
                    </div>
                @else
                    <span class="status">Belum dikerjakan</span>
                @endif
            </article>
        @endforeach
    </section>
</body>
</html>
