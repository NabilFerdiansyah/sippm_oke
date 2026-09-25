<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIPPM &middot; @yield('pageTitle', 'Dashboard')</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="app" id="appShell">

  @include('partials.sidebar')

  <div class="main">
    <div class="topbar">
      <div style="display:flex;align-items:center;gap:12px;">
        <button class="hamburger-btn" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Buka menu navigasi" aria-expanded="false">
          <span></span><span></span><span></span>
        </button>
        <div>
          <div class="topbar-crumb">@yield('crumb', auth()->user()->roleLabel())</div>
          <div class="topbar-title">@yield('pageTitle')</div>
        </div>
      </div>
      <div class="topbar-user">
        <div style="text-align:right;">
          <div style="font-weight:600;">{{ auth()->user()->name }}</div>
          <div style="font-size:11px;color:var(--ink-soft);">{{ auth()->user()->roleSubLabel() }}</div>
        </div>
        <div class="avatar">{{ auth()->user()->initials() }}</div>
      </div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar(false)"></div>

    <div class="content">

      @if (session('success'))
        <div class="callout success" style="margin-bottom:16px;">{{ session('success') }}</div>
      @endif
      @if (session('warning'))
        <div class="callout warn" style="margin-bottom:16px;">{{ session('warning') }}</div>
      @endif

      <!-- ============ HERO BANNER PERSISTEN (tampil di semua halaman) ============ -->
      <div class="page-hero" id="pageHero">
        <div class="page-hero-body">
          <div>
            <div class="page-hero-eyebrow">PG Rendeng &middot; Sinergi Gula Nusantara</div>
            <div class="page-hero-title">@yield('heroTitle')</div>
            <div class="page-hero-sub">@yield('heroSub', '&nbsp;')</div>
          </div>
          <div class="page-hero-badge"><span class="dot"></span><span>@yield('heroBadge', auth()->user()->roleSubLabel())</span></div>
        </div>
      </div>

      <section class="screen active">
        @yield('content')
      </section>

    </div>
  </div>
</div>

<script>
  // Buka/tutup sidebar mobile — identik dengan mockup.
  function toggleSidebar(forceState){
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const btn = document.getElementById('hamburgerBtn');
    if(!sidebar || !overlay || !btn) return;
    const shouldOpen = typeof forceState === 'boolean' ? forceState : !sidebar.classList.contains('sidebar-open');
    sidebar.classList.toggle('sidebar-open', shouldOpen);
    overlay.classList.toggle('is-active', shouldOpen);
    btn.classList.toggle('is-active', shouldOpen);
    btn.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
  }
</script>
<script src="{{ asset('js/app.js') }}"></script>
@yield('scripts')
</body>
</html>
