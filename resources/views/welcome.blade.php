<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Bengkel System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg: #f4f6fb;
            --surface: #ffffff;
            --primary: #2563eb;
            --primary-soft: #dbeafe;
            --text: #0f172a;
            --muted: #64748b;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --radius: 14px;
            --shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
            --border: 1px solid #e2e8f0;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            padding-bottom: 88px;
        }

        .container {
            width: min(1120px, 92%);
            margin: 0 auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: var(--surface);
            border-bottom: var(--border);
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: 64px;
        }

        .brand { font-weight: 700; display: flex; align-items: center; gap: .5rem; }
        .brand i { color: var(--primary); }

        .desktop-menu { display: none; gap: .75rem; }
        .desktop-menu button {
            border: 0;
            background: transparent;
            padding: .55rem .85rem;
            border-radius: 10px;
            color: var(--muted);
            cursor: pointer;
            font-weight: 600;
        }
        .desktop-menu button.active, .desktop-menu button:hover {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .page-title { margin: 1.1rem 0; font-size: 1.2rem; font-weight: 700; }

        .cards { display: grid; gap: 1rem; grid-template-columns: 1fr; }

        .stat-card {
            background: var(--surface);
            border: var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1rem;
        }

        .stat-title { color: var(--muted); font-size: .9rem; }
        .stat-value { margin-top: .4rem; font-size: 1.35rem; font-weight: 700; }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 999px;
            padding: .3rem .7rem;
            font-size: .75rem;
            font-weight: 700;
            margin-top: .55rem;
        }
        .badge.success { background: #d1fae5; color: #047857; }
        .badge.warning { background: #fef3c7; color: #b45309; }

        .panel {
            margin-top: 1.1rem;
            background: var(--surface);
            border: var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .panel-head {
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .7rem;
            border-bottom: var(--border);
        }

        .btn {
            border: 0;
            border-radius: 10px;
            padding: .6rem .9rem;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
        }

        .btn-primary { background: var(--primary); color: #fff; }
        .btn-danger { background: #fee2e2; color: #b91c1c; }
        .btn-light { background: #f1f5f9; color: #334155; }

        .table-wrap { width: 100%; overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }
        th, td {
            text-align: left;
            padding: .9rem 1rem;
            border-bottom: var(--border);
            font-size: .92rem;
            vertical-align: top;
        }
        th { color: var(--muted); font-weight: 600; background: #f8fafc; }

        .status {
            display: inline-block;
            padding: .26rem .6rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 700;
        }
        .status.pending { background: #fef3c7; color: #b45309; }
        .status.done { background: #d1fae5; color: #047857; }
        .status.process { background: #dbeafe; color: #1d4ed8; }

        .table-cards { display: none; padding: 1rem; gap: .8rem; }

        .job-card {
            border: var(--border);
            border-radius: 12px;
            padding: .9rem;
            background: #fff;
        }

        .job-card h4 { margin: 0 0 .55rem; font-size: 1rem; }
        .kv {
            display: flex;
            justify-content: space-between;
            gap: .5rem;
            margin-bottom: .35rem;
            font-size: .85rem;
        }
        .kv .key { color: var(--muted); }

        .mobile-nav {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--surface);
            border-top: var(--border);
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            z-index: 30;
        }
        .mobile-nav button {
            border: 0;
            background: transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .2rem;
            padding: .7rem .2rem;
            color: var(--muted);
            font-size: .72rem;
            cursor: pointer;
        }
        .mobile-nav button.active { color: var(--primary); font-weight: 700; }

        @media (min-width: 768px) {
            body { padding-bottom: 1.2rem; }
            .desktop-menu { display: flex; }
            .cards { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .mobile-nav { display: none; }
        }

        @media (max-width: 767px) {
            .table-wrap { display: none; }
            .table-cards { display: grid; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <div class="brand"><i class="bi bi-tools"></i> Bengkel System</div>
            <nav class="desktop-menu" id="desktopMenu">
                <button class="active"><i class="bi bi-speedometer2"></i> Dashboard</button>
                <button><i class="bi bi-clipboard-check"></i> Pekerjaan</button>
                <button><i class="bi bi-people"></i> Pelanggan</button>
                <button><i class="bi bi-gear"></i> Pengaturan</button>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1 class="page-title">Template Responsive (Vanilla CSS + JS)</h1>

        <section class="cards">
            <article class="stat-card">
                <div class="stat-title">Total Antrian Hari Ini</div>
                <div class="stat-value">28</div>
                <span class="badge success"><i class="bi bi-arrow-up-right"></i> +12% dari kemarin</span>
            </article>
            <article class="stat-card">
                <div class="stat-title">Dalam Pengerjaan</div>
                <div class="stat-value">9 Unit</div>
                <span class="badge warning"><i class="bi bi-clock-history"></i> 3 mendekati SLA</span>
            </article>
            <article class="stat-card">
                <div class="stat-title">Selesai</div>
                <div class="stat-value">16 Unit</div>
                <span class="badge success"><i class="bi bi-patch-check"></i> on-track</span>
            </article>
        </section>

        <section class="panel">
            <div class="panel-head">
                <strong>Daftar Service Kendaraan</strong>
                <div>
                    <button class="btn btn-light" id="btnInfo"><i class="bi bi-info-circle"></i> Info</button>
                    <button class="btn btn-primary" id="btnTambah"><i class="bi bi-plus-circle"></i> Tambah</button>
                </div>
            </div>

            <div class="table-wrap">
                <table id="serviceTable">
                    <thead>
                        <tr>
                            <th>No. SPK</th>
                            <th>Pelanggan</th>
                            <th>Jenis Service</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>SPK-2401</td>
                            <td>Budi Santoso</td>
                            <td>Ganti Oli + Tune Up</td>
                            <td><span class="status process">Diproses</span></td>
                            <td><button class="btn btn-danger btnDelete"><i class="bi bi-trash3"></i> Hapus</button></td>
                        </tr>
                        <tr>
                            <td>SPK-2402</td>
                            <td>Rina Wijaya</td>
                            <td>Perbaikan AC</td>
                            <td><span class="status pending">Menunggu Sparepart</span></td>
                            <td><button class="btn btn-danger btnDelete"><i class="bi bi-trash3"></i> Hapus</button></td>
                        </tr>
                        <tr>
                            <td>SPK-2403</td>
                            <td>Andi Pratama</td>
                            <td>Body Repair</td>
                            <td><span class="status done">Selesai</span></td>
                            <td><button class="btn btn-danger btnDelete"><i class="bi bi-trash3"></i> Hapus</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-cards" id="mobileCards"></div>
        </section>
    </main>

    <nav class="mobile-nav" id="mobileNav">
        <button class="active"><i class="bi bi-speedometer2"></i><span>Home</span></button>
        <button><i class="bi bi-clipboard-check"></i><span>Order</span></button>
        <button><i class="bi bi-bell"></i><span>Notif</span></button>
        <button><i class="bi bi-person"></i><span>Akun</span></button>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const table = document.getElementById('serviceTable');
        const mobileCards = document.getElementById('mobileCards');

        function renderCardsFromTable() {
            const rows = [...table.querySelectorAll('tbody tr')];
            mobileCards.innerHTML = '';

            rows.forEach((row) => {
                const cells = row.querySelectorAll('td');
                const card = document.createElement('article');
                card.className = 'job-card';

                card.innerHTML = `
                    <h4>${cells[0].textContent}</h4>
                    <div class="kv"><span class="key">Pelanggan</span><strong>${cells[1].textContent}</strong></div>
                    <div class="kv"><span class="key">Service</span><span>${cells[2].textContent}</span></div>
                    <div class="kv"><span class="key">Status</span><span>${cells[3].innerHTML}</span></div>
                    <button class="btn btn-danger btnDelete"><i class="bi bi-trash3"></i> Hapus</button>
                `;

                mobileCards.appendChild(card);
            });

            bindDeleteButton();
        }

        function bindDeleteButton() {
            document.querySelectorAll('.btnDelete').forEach((button) => {
                button.onclick = () => {
                    Swal.fire({
                        title: 'Hapus data ini?',
                        text: 'Data SPK akan dihapus permanen.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire('Berhasil', 'Data SPK sudah dihapus.', 'success');
                        }
                    });
                };
            });
        }

        document.getElementById('btnTambah').addEventListener('click', () => {
            Swal.fire({
                title: 'Tambah Service',
                text: 'Nanti bisa diarahkan ke form input service.',
                icon: 'info'
            });
        });

        document.getElementById('btnInfo').addEventListener('click', () => {
            Swal.fire({
                title: 'Template Siap Pakai',
                html: 'Desktop: navbar di atas.<br>Mobile: navbar pindah ke bawah + tabel jadi card.',
                icon: 'success'
            });
        });

        document.querySelectorAll('#mobileNav button').forEach((button) => {
            button.addEventListener('click', () => {
                document.querySelectorAll('#mobileNav button').forEach((btn) => btn.classList.remove('active'));
                button.classList.add('active');
            });
        });

        renderCardsFromTable();
        bindDeleteButton();
    </script>
</body>
</html>
