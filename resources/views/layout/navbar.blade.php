@php
    $role = auth()->user()?->role ?? 'pelanggan';
    $menu = [
        ['label' => 'Work Order', 'icon' => 'bi-clipboard-check', 'route' => 'workorder.index'],
        ['label' => 'Laporan', 'icon' => 'bi-file-earmark-text', 'route' => 'laporan.index'],
        ['label' => 'Kwitansi', 'icon' => 'bi-receipt', 'route' => 'kwitansi.index'],
    ];

    if ($role === 'admin') {
        $menu[] = ['label' => 'User', 'icon' => 'bi-people', 'route' => 'user.index'];
    }
@endphp

<header class="topbar">
    <div class="container topbar-inner">
        <div class="brand">
            <img src="{{ asset('images/reno-motor-logo.svg') }}" alt="Logo Reno Motor">
            <span>Reno Motor</span>
        </div>
        <nav class="desktop-menu">
            @foreach ($menu as $item)
                <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['route']) ? 'active' : '' }}">
                    <i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
                </a>
            @endforeach
            <form action="{{ route('logout') }}" method="POST" style="display:inline-block;">
                @csrf
                <button type="submit" class="btn btn-light" style="padding:.55rem .8rem;">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </nav>
    </div>
</header>

<nav class="mobile-nav" style="grid-template-columns: repeat({{ count($menu) + 1 }}, 1fr);">
    @foreach ($menu as $item)
        <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['route']) ? 'active' : '' }}">
            <i class="bi {{ $item['icon'] }}"></i>
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
    <form action="{{ route('logout') }}" method="POST" style="display:flex; align-items:center; justify-content:center;">
        @csrf
        <button type="submit" style="border:0; background:none; color:var(--muted); display:flex; flex-direction:column; align-items:center; gap:.2rem; font-size:.7rem; cursor:pointer; padding:.72rem .2rem;">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </button>
    </form>
</nav>
