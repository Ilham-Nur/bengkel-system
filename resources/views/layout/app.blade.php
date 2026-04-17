<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bengkel Motor')</title>
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
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --border: 1px solid #e2e8f0;
            --radius: 14px;
            --shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            padding-bottom: 88px;
        }
        .container { width: min(1120px, 92%); margin: 0 auto; }
        .page-title { margin: 1.1rem 0; font-size: 1.22rem; font-weight: 700; }
        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            background: var(--surface);
            border-bottom: var(--border);
        }
        .topbar-inner {
            min-height: 64px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
        }
        .brand { font-weight: 700; display: flex; align-items: center; gap: .5rem; }
        .brand i { color: var(--primary); }
        .desktop-menu { display: none; gap: .5rem; flex-wrap: wrap; }
        .desktop-menu a {
            text-decoration: none;
            color: var(--muted);
            font-weight: 600;
            padding: .55rem .8rem;
            border-radius: 10px;
        }
        .desktop-menu a.active,
        .desktop-menu a:hover { background: var(--primary-soft); color: var(--primary); }

        .mobile-nav {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--surface);
            border-top: var(--border);
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            z-index: 20;
        }
        .mobile-nav a {
            text-decoration: none;
            color: var(--muted);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .2rem;
            padding: .72rem .2rem;
            font-size: .7rem;
        }
        .mobile-nav a.active { color: var(--primary); font-weight: 700; }

        .panel {
            background: var(--surface);
            border: var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 1rem;
        }
        .panel-head {
            padding: 1rem;
            border-bottom: var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
        }
        .table-wrap { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 680px; }
        th, td { padding: .85rem 1rem; border-bottom: var(--border); font-size: .92rem; vertical-align: top; }
        th { background: #f8fafc; color: var(--muted); text-align: left; }

        .card-list { display: none; padding: 1rem; gap: .8rem; }
        .info-card { border: var(--border); border-radius: 12px; padding: .9rem; }
        .info-card h4 { margin: 0 0 .6rem; font-size: 1rem; }
        .kv { display: flex; justify-content: space-between; gap: .4rem; margin-bottom: .35rem; font-size: .86rem; }
        .kv .key { color: var(--muted); }

        .btn {
            border: 0;
            border-radius: 10px;
            cursor: pointer;
            padding: .56rem .85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .86rem;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-light { background: #f1f5f9; color: #334155; }
        .btn-danger { background: #fee2e2; color: #b91c1c; }
        .btn-success { background: #d1fae5; color: #047857; }
        .btn-warning { background: #fef3c7; color: #b45309; }

        .status {
            display: inline-block;
            border-radius: 999px;
            padding: .24rem .62rem;
            font-size: .74rem;
            font-weight: 700;
        }
        .status.draft { background: #e2e8f0; color: #334155; }
        .status.process { background: #dbeafe; color: #1d4ed8; }
        .status.done { background: #d1fae5; color: #047857; }

        .form-page {
            max-width: 700px;
            margin: 1.1rem auto;
            background: var(--surface);
            border: var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1rem;
        }
        .form-grid { display: grid; gap: .8rem; grid-template-columns: 1fr; }
        label { display: block; margin-bottom: .35rem; color: #334155; font-size: .85rem; font-weight: 600; }
        .input, textarea, select {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: .6rem .72rem;
            font-family: inherit;
            font-size: .92rem;
            background: #fff;
        }
        textarea { min-height: 96px; resize: vertical; }
        .form-action { margin-top: 1rem; display: flex; gap: .6rem; flex-wrap: wrap; }

        .role-box {
            margin: .85rem 0;
            padding: .7rem .9rem;
            border-radius: 12px;
            border: var(--border);
            background: #fff;
            font-size: .86rem;
            color: var(--muted);
        }

        @media (min-width: 768px) {
            body { padding-bottom: 1rem; }
            .desktop-menu { display: flex; }
            .mobile-nav { display: none; }
            .form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .full { grid-column: 1 / -1; }
        }

        @media (max-width: 767px) {
            .desktop-only { display: none; }
            .card-list { display: grid; }
        }
    </style>
</head>
<body>
    @unless(isset($hideNavbar) && $hideNavbar)
        @include('layout.navbar')
    @endunless

    <main class="container">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json(session('success')),
                timer: 2200,
                showConfirmButton: false
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: @json(session('error'))
            });
        </script>
    @endif
    @stack('scripts')
</body>
</html>
