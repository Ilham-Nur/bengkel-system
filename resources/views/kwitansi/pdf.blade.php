<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi {{ $kwitansi->no_invoice }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #0f172a; }
        .toolbar { margin-bottom: 14px; display: flex; gap: 8px; }
        .btn { border: 0; border-radius: 6px; padding: 8px 12px; background: #1d4ed8; color: #fff; cursor: pointer; }
        .btn-light { background: #e2e8f0; color: #0f172a; text-decoration: none; }
        .card { border: 1px solid #cbd5e1; border-radius: 10px; padding: 12px; margin-bottom: 12px; position: relative; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        .meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; font-size: 13px; margin-top: 10px; }
        .meta div { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; font-size: 12px; text-align: left; }
        th { background: #f8fafc; }
        .text-right { text-align: right; }
        .status { margin-top: 10px; font-size: 13px; }
        .stamp {
            position: absolute;
            right: 20px;
            top: 18px;
            width: 145px;
            opacity: 0.9;
            transform: rotate(-12deg);
        }
        .stamp-fallback {
            position: absolute;
            right: 14px;
            top: 14px;
            border: 3px solid #dc2626;
            color: #dc2626;
            border-radius: 10px;
            padding: 6px 12px;
            font-size: 24px;
            font-weight: 800;
            transform: rotate(-10deg);
        }
        @media print {
            .toolbar { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn" onclick="window.print()">Export / Simpan PDF</button>
        <a class="btn btn-light" href="{{ route('kwitansi.index') }}">Kembali</a>
    </div>

    <section class="card">
        <h1>Kwitansi / Invoice {{ $kwitansi->no_invoice }}</h1>
        <div style="color:#475569; font-size:13px; margin-bottom: 10px;">Dokumen siap cetak PDF.</div>

        @if ($kwitansi->is_paid)
            @if (file_exists(public_path('images/stamp-lunas.png')))
                <img src="{{ asset('images/stamp-lunas.png') }}" alt="Stamp Lunas" class="stamp">
            @else
                <div class="stamp-fallback">LUNAS</div>
            @endif
        @endif

        <div class="meta">
            <div><strong>No WO:</strong><br>{{ $kwitansi->workOrder?->no_wo ?? '-' }}</div>
            <div><strong>Tanggal:</strong><br>{{ $kwitansi->tanggal }}</div>
            <div><strong>Nama Customer:</strong><br>{{ $kwitansi->customer_name }}</div>
            <div><strong>No HP:</strong><br>{{ $kwitansi->customer_phone ?: '-' }}</div>
            <div><strong>Jenis Motor:</strong><br>{{ $kwitansi->jenis_motor }}</div>
            <div><strong>Plat Nomor:</strong><br>{{ $kwitansi->plat_nomor }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:50px;">No</th>
                    <th>Item</th>
                    <th style="width:80px;" class="text-right">Qty</th>
                    <th style="width:140px;" class="text-right">Harga</th>
                    <th style="width:160px;" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kwitansi->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td class="text-right">{{ number_format($item->qty, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; color:#64748b;">Belum ada detail item.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Total</th>
                    <th class="text-right">Rp {{ number_format($kwitansi->total_kwitansi, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <div class="status">
            <strong>Status Pembayaran:</strong>
            {{ $kwitansi->is_paid ? 'Lunas' : 'Belum Lunas' }}
            @if ($kwitansi->paid_at)
                ({{ $kwitansi->paid_at->format('d-m-Y H:i') }})
            @endif
        </div>
    </section>
</body>
</html>
