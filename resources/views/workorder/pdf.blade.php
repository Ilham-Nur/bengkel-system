<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order {{ $workOrder->no_wo }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #0f172a; }
        .toolbar { margin-bottom: 14px; display: flex; gap: 8px; }
        .btn { border: 0; border-radius: 6px; padding: 8px 12px; background: #1d4ed8; color: #fff; cursor: pointer; }
        .btn-light { background: #e2e8f0; color: #0f172a; text-decoration: none; }
        .card { border: 1px solid #cbd5e1; border-radius: 10px; padding: 12px; margin-bottom: 12px; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        h2 { font-size: 16px; margin: 0 0 8px; }
        .meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; font-size: 13px; }
        .meta div { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px; }
        .complaint { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; margin-top: 10px; }
        .label { font-size: 12px; color: #475569; margin-bottom: 2px; }
        .value { font-size: 13px; }
        .photo-grid {
            margin-top: 8px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }
        .photo-card {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 6px;
            background: #fff;
        }
        .photo-box {
            width: 100%;
            aspect-ratio: 1 / 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            image-rendering: auto;
        }
        .photo-desc { margin-top: 6px; font-size: 11px; color: #334155; min-height: 24px; }
        .total { margin-top: 10px; font-weight: 700; }
        @media print {
            .toolbar { display: none; }
            body { margin: 0; }
            .card { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn" onclick="window.print()">Export / Simpan PDF</button>
        <a class="btn btn-light" href="{{ route('workorder.index') }}">Kembali</a>
    </div>

    <section class="card">
        <h1>Work Order {{ $workOrder->no_wo }}</h1>
        <div style="color:#475569; font-size:13px; margin-bottom: 10px;">Dokumen siap cetak PDF.</div>
        <div class="meta">
            <div><strong>Pelanggan:</strong><br>{{ $workOrder->customer?->name ?? '-' }}</div>
            <div><strong>Tanggal:</strong><br>{{ $workOrder->tanggal }}</div>
            <div><strong>Jenis Motor:</strong><br>{{ $workOrder->jenis_motor }}</div>
            <div><strong>Plat Nomor:</strong><br>{{ $workOrder->plat_nomor }}</div>
            <div><strong>KM Motor:</strong><br>{{ number_format($workOrder->km_motor, 0, ',', '.') }}</div>
            <div><strong>Total Estimasi:</strong><br>Rp {{ number_format($workOrder->total_keluhan_biaya, 0, ',', '.') }}</div>
        </div>
    </section>

    <section class="card">
        <h2>Detail Keluhan</h2>

        @foreach ($workOrder->complaintItems as $index => $item)
            <article class="complaint">
                <div class="value" style="font-weight:700; margin-bottom: 6px;">Keluhan #{{ $index + 1 }}</div>
                <div class="label">Keluhan Item</div>
                <div class="value">{{ $item->keluhan_item }}</div>

                <div class="label" style="margin-top:6px;">Rekomendasi Perbaikan</div>
                <div class="value">{{ $item->rekomendasi_perbaikan ?: '-' }}</div>

                <div class="label" style="margin-top:6px;">Sparepart</div>
                <div class="value">{{ $item->sparepart ?: '-' }}</div>

                <div class="label" style="margin-top:6px;">Estimasi Biaya</div>
                <div class="value">Rp {{ number_format($item->estimasi_biaya, 0, ',', '.') }}</div>

                @if ($item->photos->isNotEmpty())
                    <div class="photo-grid">
                        @foreach ($item->photos as $photo)
                            <div class="photo-card">
                                <div class="photo-box">
                                    <img src="{{ asset('storage/'.$photo->photo_path) }}" alt="Foto komponen">
                                </div>
                                <div class="photo-desc">{{ $photo->photo_description ?: 'Tanpa deskripsi' }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </article>
        @endforeach

        <div class="total">Total Keluhan Biaya: Rp {{ number_format($workOrder->total_keluhan_biaya, 0, ',', '.') }}</div>
    </section>
</body>
</html>
