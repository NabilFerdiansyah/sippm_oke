@php
    $role = auth()->user()->role;

    $navConfig = [
        'operator' => [
            ['group' => 'Menu', 'items' => [
                ['route' => 'operator.dashboard', 'label' => 'Dashboard', 'ic' => '▤'],
                ['route' => 'operator.laporan.create', 'label' => 'Buat Laporan', 'ic' => '✎'],
            ]],
            ['group' => 'Akun', 'items' => [
                ['route' => 'profil.edit', 'label' => 'Profil Saya', 'ic' => '⚙'],
            ]],
        ],
        'manager' => [
            ['group' => 'Operasional', 'items' => [
                ['route' => 'manager.dashboard', 'label' => 'Dashboard', 'ic' => '▤'],
                ['route' => 'manager.dashboard', 'params' => ['status' => 'menunggu_validasi_akhir'], 'label' => 'Validasi Akhir', 'ic' => '✔'],
            ]],
            ['group' => 'Riwayat', 'items' => [
                ['route' => 'manager.histori', 'label' => 'Histori Laporan', 'ic' => '☰'],
            ]],
            ['group' => 'Manajemen Akun', 'items' => [
                ['route' => 'manager.akun.index', 'label' => 'Kelola Akun', 'ic' => '👤'],
            ]],
            ['group' => 'Akun', 'items' => [
                ['route' => 'profil.edit', 'label' => 'Profil Saya', 'ic' => '⚙'],
            ]],
        ],
        'teknisi' => [
            ['group' => 'Menu', 'items' => [
                ['route' => 'teknisi.dashboard', 'label' => 'Tugas Saya', 'ic' => '▤'],
                ['route' => 'teknisi.hasil.index', 'label' => 'Input Hasil Penanganan', 'ic' => '✎'],
                ['route' => 'teknisi.riwayat', 'label' => 'Riwayat', 'ic' => '☰'],
            ]],
            ['group' => 'Akun', 'items' => [
                ['route' => 'profil.edit', 'label' => 'Profil Saya', 'ic' => '⚙'],
            ]],
        ],
    ];
@endphp

<aside class="sidebar">
  <div class="brand">
    <div class="brand-mark">SIP<span>PM</span></div>
    <div class="brand-sub">PG Rendeng &middot; Sinergi Gula Nusantara</div>
  </div>

  <div class="sidebar-divider"></div>

  <div class="sidebar-block sidebar-block-menu">
    <div class="sidebar-block-label">Menu &amp; Fitur</div>
    <nav class="nav" id="navArea">
      @foreach ($navConfig[$role] as $group)
        <div class="nav-group-label">{{ $group['group'] }}</div>
        @foreach ($group['items'] as $item)
          @php $active = request()->routeIs($item['route']) && (!isset($item['params']) || request()->query('status') === ($item['params']['status'] ?? null)); @endphp
          <a href="{{ route($item['route'], $item['params'] ?? []) }}" class="nav-item{{ $active ? ' active' : '' }}">
            <span class="ic">{{ $item['ic'] }}</span>{{ $item['label'] }}
          </a>
        @endforeach
      @endforeach
    </nav>
  </div>

  <div class="sidebar-foot">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn-logout">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Keluar (Logout)
      </button>
    </form>
    <div class="sidebar-foot-note">SIPPM &mdash; Sistem Informasi Pelaporan &amp; Penanganan Kerusakan Mesin Giling.<br>PG Rendeng &middot; Sinergi Gula Nusantara.</div>
  </div>
</aside>
