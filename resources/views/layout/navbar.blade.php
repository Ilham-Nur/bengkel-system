@php
    $menu = [
        ['label' => 'Work Order', 'icon' => 'bi-clipboard-check', 'route' => 'workorder.index'],
        ['label' => 'Laporan', 'icon' => 'bi-file-earmark-text', 'route' => 'laporan.index'],
        ['label' => 'Kwitansi', 'icon' => 'bi-receipt', 'route' => 'kwitansi.index'],
        ['label' => 'Pelanggan', 'icon' => 'bi-people', 'route' => 'pelanggan.index'],
    ];
@endphp

<header class="topbar">
    <div class="container topbar-inner">
        <div class="brand"><i class="bi bi-tools"></i> Bengkel Motor</div>
        <nav class="desktop-menu">
            @foreach ($menu as $item)
                <a href="{{ route($item['route'], ['role' => request('role', 'admin')]) }}" class="{{ request()->routeIs($item['route']) ? 'active' : '' }}">
                    <i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
                </a>
            @endforeach
            <a href="{{ route('login.index') }}"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </nav>
    </div>
</header>

<nav class="mobile-nav">
    @foreach ($menu as $item)
        <a href="{{ route($item['route'], ['role' => request('role', 'admin')]) }}" class="{{ request()->routeIs($item['route']) ? 'active' : '' }}">
            <i class="bi {{ $item['icon'] }}"></i>
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
    <a href="{{ route('login.index') }}">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </a>
</nav>
