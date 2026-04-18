<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi {{ $kwitansi->no_invoice }}</title>
    <style>
        :root {
            --line: #6b7280;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 16px;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            background: #f3f4f6;
        }

        .toolbar {
            margin-bottom: 10px;
            display: flex;
            gap: 8px;
        }

        .btn {
            border: 0;
            border-radius: 6px;
            padding: 8px 12px;
            background: #1d4ed8;
            color: #fff;
            cursor: pointer;
        }

        .btn-light {
            background: #e5e7eb;
            color: #111827;
            text-decoration: none;
        }

        .sheet {
            position: relative;
            width: 100%;
            max-width: 920px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid var(--line);
        }

        .header {
            display: grid;
            grid-template-columns: 1fr 180px;
            border-bottom: 1px solid var(--line);
        }

        .brand-area {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            border-right: 1px solid var(--line);
        }

        .logo {
            width: 66px;
            height: 66px;
            border: 1px dashed #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #6b7280;
            overflow: hidden;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-title {
            font-weight: 900;
            font-size: 38px;
            color: #ef4444;
            line-height: 1;
            letter-spacing: .5px;
            font-style: italic;
        }

        .brand-sub {
            font-size: 13px;
            font-weight: 700;
            margin-top: 2px;
            letter-spacing: .2px;
        }

        .brand-addr {
            font-size: 11px;
            margin-top: 3px;
            color: #374151;
        }

        .invoice-box {
            padding: 8px;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }

        .invoice-box span {
            font-size: 14px;
        }

        .info-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid var(--line);
        }

        .info-col {
            padding: 8px;
            min-height: 120px;
        }

        .info-col+.info-col {
            border-left: 1px solid var(--line);
        }

        .row {
            display: grid;
            grid-template-columns: 110px 10px 1fr;
            gap: 6px;
            align-items: end;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .dots {
            border-bottom: 1px dotted #6b7280;
            min-height: 17px;
            display: flex;
            align-items: flex-end;
            padding: 0 2px 1px;
            font-weight: 600;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th,
        .items-table td {
            border: 1px solid var(--line);
            font-size: 12px;
            padding: 6px;
            vertical-align: top;
        }

        .items-table th {
            text-align: center;
            background: #f9fafb;
            font-weight: 700;
        }

        .items-table tbody td {
            height: 32px;
        }

        .text-right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .notes {
            display: grid;
            grid-template-columns: 1fr 180px 180px;
            border-top: 1px solid var(--line);
            min-height: 78px;
        }

        .notes-left {
            padding: 8px;
            font-size: 10px;
            line-height: 1.4;
        }

        .sign {
            border-left: 1px solid var(--line);
            padding: 8px;
            font-size: 11px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .signature-line {
            border-top: 1px dotted #6b7280;
            margin-top: 26px;
            padding-top: 3px;
        }

        .promo {
            border-top: 1px solid var(--line);
            padding: 6px 8px;
            font-size: 12px;
            font-weight: 700;
            color: #ef4444;
        }

        .stamp {
            position: absolute;
            right: 90px;
            top: 290px;
            width: 180px;
            opacity: .88;
            transform: rotate(-12deg);
        }

        .stamp-fallback {
            position: absolute;
            right: 90px;
            top: 400px;
            border: 3px solid #dc2626;
            color: #dc2626;
            border-radius: 10px;
            padding: 6px 12px;
            font-size: 24px;
            font-weight: 800;
            transform: rotate(-10deg);
            background: rgba(255, 255, 255, .7);
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .toolbar {
                display: none;
            }

            .sheet {
                max-width: none;
                border: 0;
            }
        }
    </style>
</head>

<body>

    @php
        function toBase64($path)
        {
            if (!file_exists($path)) {
                return null;
            }
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $logo = toBase64(public_path('images/reno_motor.jpeg'));
        $stamp = toBase64(public_path('images/stamp_lunas.png'));
    @endphp


    <div class="toolbar">
        <button class="btn" onclick="window.print()">Export / Simpan PDF</button>
        <a class="btn btn-light" href="{{ route('kwitansi.index') }}">Kembali</a>
    </div>

    <section class="sheet">
        @if ($kwitansi->is_paid)
            @if (file_exists(public_path('images/stamp_lunas.png')))
                <img src="{{ $stamp }}" alt="Stamp Lunas" class="stamp">
            @else
                <div class="stamp-fallback">LUNAS</div>
            @endif
        @endif

        <div class="header">
            <div class="brand-area">
                <div class="logo">
                    @if (file_exists(public_path('images/reno_motor.jpeg')))
                        <img src="{{ $logo }}" alt="Logo">
                    @else
                        LOGO
                    @endif
                </div>
                <div>
                    <div class="brand-title">Kanaya Motor</div>
                    <div class="brand-sub">MELAYANI SERVICE MOTOR DAN PENJUALAN SPARE PART</div>
                    <div class="brand-addr">Alamat: Isi dari profil bengkel · Telp/WA: Isi kontak bengkel</div>
                </div>
            </div>
            <div class="invoice-box">
                <div>NO FAKTUR :<br><span>{{ $kwitansi->no_invoice }}</span></div>
            </div>
        </div>

        <div class="info-wrap">
            <div class="info-col">
                <div class="row">
                    <div>Nama Pemilik</div>
                    <div>:</div>
                    <div class="dots">{{ $kwitansi->customer_name }}</div>
                </div>
                <div class="row">
                    <div>Tanggal Masuk</div>
                    <div>:</div>
                    <div class="dots">{{ \Illuminate\Support\Carbon::parse($kwitansi->tanggal)->format('d-m-Y') }}
                    </div>
                </div>
                <div class="row">
                    <div>Dikerjakan Oleh</div>
                    <div>:</div>
                    <div class="dots">-</div>
                </div>
            </div>
            <div class="info-col">
                <div class="row">
                    <div>Merk Kendaraan</div>
                    <div>:</div>
                    <div class="dots">{{ $kwitansi->jenis_motor }}</div>
                </div>
                <div class="row">
                    <div>No. Polisi</div>
                    <div>:</div>
                    <div class="dots">{{ $kwitansi->plat_nomor }}</div>
                </div>
                <div class="row">
                    <div>KM Kendaraan</div>
                    <div>:</div>
                    <div class="dots">{{ number_format($kwitansi->workOrder?->km_motor ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:110px;">BANYAKNYA</th>
                    <th>KETERANGAN</th>
                    <th style="width:170px;">HARGA SATUAN</th>
                    <th style="width:170px;">JUMLAH HARGA</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $rows = max(6, $kwitansi->items->count());
                @endphp
                @for ($i = 0; $i < $rows; $i++)
                    @php $item = $kwitansi->items[$i] ?? null; @endphp
                    <tr>
                        <td class="center">{{ $item ? number_format($item->qty, 0, ',', '.') : '' }}</td>
                        <td>{{ $item?->item_name }}</td>
                        <td class="text-right">{{ $item ? 'Rp ' . number_format($item->unit_price, 0, ',', '.') : '' }}
                        </td>
                        <td class="text-right">{{ $item ? 'Rp ' . number_format($item->subtotal, 0, ',', '.') : '' }}
                        </td>
                    </tr>
                @endfor
                <tr>
                    <td colspan="3" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($kwitansi->total_kwitansi, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="notes">
            <div class="notes-left">
                <strong>Catatan:</strong><br>
                1. Barang yang sudah dibeli tidak dapat dikembalikan.<br>
                2. Komplain maksimal 24 jam setelah selesai pengerjaan.<br>
                Status: {{ $kwitansi->is_paid ? 'Lunas' : 'Belum Lunas' }}
                @if ($kwitansi->paid_at)
                    ({{ $kwitansi->paid_at->format('d-m-Y H:i') }})
                @endif
            </div>
            <div class="sign">
                <div>Pemilik,</div>
                <div class="signature-line">{{ $kwitansi->customer_name }}</div>
            </div>
            <div class="sign">
                <div>Kepala Mekanik,</div>
                <div class="signature-line">________________</div>
            </div>
        </div>

        <div class="promo">10X SERVICE GRATIS 1X GANTI OLI</div>
    </section>
</body>

</html>
