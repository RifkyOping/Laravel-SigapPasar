<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sigap Pasar — Sistem Informasi Pengawasan dan Penanganan Isu Pasar">
    <title>{{ $title ?? 'Dashboard' }} — Sigap Pasar</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function() {
            try {
                var savedTheme = localStorage.getItem('sigap_theme');
                if (savedTheme === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            } catch (e) {}
        })();
    </script>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<aside class="sp-sidebar" id="sp-sidebar" aria-label="Navigasi utama">
    {{-- Logo --}}
    <div class="sp-sidebar-logo">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.6rem;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SIGAP PASAR" style="height: 38px; width: auto; object-fit: contain;">
            <div>
                <div class="wordmark" style="font-size: 1.15rem; line-height: 1.1;">SIGAP<span style="color: #4caf50;">PASAR</span></div>
                <div style="font-size: 0.65rem; color: #81c784; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">Sulawesi Barat</div>
            </div>
        </div>
        <div class="tagline" style="font-size: 0.63rem; line-height: 1.35; color: var(--clr-ink-3);">Dinas Koperasi & UKM, Perindustrian & Perdagangan</div>
    </div>

    {{-- Navigation --}}
    <nav class="sp-nav" role="navigation">

        {{-- Semua Role --}}
        <div class="sp-nav-section">{{ auth()->user()->isPimpinan() ? 'Eksekutif' : 'Beranda' }}</div>
        <a href="{{ route('dashboard') }}"
           class="sp-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            {{ auth()->user()->isPimpinan() ? 'Ringkasan Eksekutif' : 'Dashboard' }}
        </a>

        {{-- Pengawas Siber + Admin --}}
        @if(!auth()->user()->isPimpinan() && auth()->user()->isRole(['pengawas_siber','admin','super_admin']))
        <div class="sp-nav-section">Isu</div>
        <a href="{{ route('issues.index') }}"
           class="sp-nav-link {{ (request()->routeIs('issues.*') && !request()->routeIs('issues.create')) ? 'active' : '' }}"
           aria-current="{{ (request()->routeIs('issues.*') && !request()->routeIs('issues.create')) ? 'page' : 'false' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Semua Isu
        </a>
        @endif

        @if(auth()->user()->isPengawasSiber() || auth()->user()->isRole(['admin','super_admin']))
        <a href="{{ route('issues.create') }}"
           class="sp-nav-link {{ request()->routeIs('issues.create') ? 'active' : '' }}"
           aria-current="{{ request()->routeIs('issues.create') ? 'page' : 'false' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            Input Isu Baru
        </a>
        @endif

        {{-- Admin --}}
        @if(auth()->user()->isRole(['admin','super_admin']))
        <div class="sp-nav-section">Operasional</div>
        <a href="{{ route('penugasan.index') }}"
           class="sp-nav-link {{ request()->routeIs('penugasan.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            <span style="flex:1;">Penugasan</span>
            @php $pCount = \App\Models\Issue::whereIn('status', ['baru', 'diverifikasi'])->count(); @endphp
            @if($pCount > 0)
            <span class="sp-badge badge-baru" style="font-size:0.65rem;padding:0.1rem 0.45rem;border-radius:999px;">{{ $pCount }}</span>
            @endif
        </a>
        <a href="{{ route('intervensi.index') }}"
           class="sp-nav-link {{ request()->routeIs('intervensi.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            <span style="flex:1;">Intervensi</span>
            @php
                $iCount = \App\Models\Issue::whereIn('status', ['di_lapangan', 'ditugaskan', 'diverifikasi'])
                    ->whereDoesntHave('interventions')
                    ->count();
            @endphp
            @if($iCount > 0)
            <span class="sp-badge badge-kritis" style="font-size:0.65rem;padding:0.1rem 0.45rem;border-radius:999px;">{{ $iCount }}</span>
            @endif
        </a>
        @endif

        {{-- Petugas Lapangan --}}
        @if(auth()->user()->isPetugasLapangan())
        <div class="sp-nav-section">Penugasan Saya</div>
        <a href="{{ route('laporan.index') }}"
           class="sp-nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span style="flex:1;">Laporan Lapangan</span>
            @php
                $lCount = \App\Models\FieldAssignment::where('petugas_id', auth()->id())
                    ->whereIn('status', ['ditugaskan', 'sedang_dikerjakan'])
                    ->count();
                $availCount = \App\Models\Issue::whereIn('status', ['baru', 'diverifikasi'])->count();
                $totalBadge = $lCount + $availCount;
            @endphp
            @if($totalBadge > 0)
            <span class="sp-badge badge-ditugaskan" style="font-size:0.65rem;padding:0.1rem 0.45rem;border-radius:999px;">{{ $totalBadge }}</span>
            @endif
        </a>
        @endif

        {{-- Pimpinan --}}
        @if(auth()->user()->isPimpinan())
        <div class="sp-nav-section">Pantauan Eksekutif</div>
        <a href="{{ route('issues.index') }}"
           class="sp-nav-link {{ request()->routeIs('issues.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <span style="flex:1;">Semua Isu Pasar</span>
            @php $isuAktifCount = \App\Models\Issue::whereNotIn('status', ['selesai', 'hoaks', 'ditutup'])->count(); @endphp
            @if($isuAktifCount > 0)
            <span class="sp-badge badge-kritis" style="font-size:0.65rem;padding:0.1rem 0.45rem;border-radius:999px;">{{ $isuAktifCount }}</span>
            @endif
        </a>
        <a href="{{ route('prices.index') }}"
           class="sp-nav-link {{ request()->routeIs('prices.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
            Data Harga Harian
        </a>
        @endif

        {{-- Administrasi & Data Harga --}}
        @if(auth()->user()->isRole(['admin', 'super_admin']))
        <div class="sp-nav-section">Data & Master</div>
        <a href="{{ route('prices.index') }}"
           class="sp-nav-link {{ request()->routeIs('prices.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
            Harga Harian
        </a>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('master-data.index') }}"
           class="sp-nav-link {{ request()->routeIs('master-data.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
            Data Master
        </a>
        <a href="{{ route('users.index') }}"
           class="sp-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Manajemen User
        </a>
        @endif
        @endif
    </nav>

    {{-- User info --}}
    <div class="sp-sidebar-user">
        <div class="sp-user-avatar" aria-hidden="true">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div style="flex:1;min-width:0;">
            <div class="sp-user-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                {{ auth()->user()->name }}
            </div>
            <div class="sp-user-role">{{ auth()->user()->role_label }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sp-btn sp-btn-ghost sp-btn-sm" title="Keluar" aria-label="Keluar dari sistem">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </button>
        </form>
    </div>
</aside>

{{-- Overlay for mobile --}}
<div class="sp-overlay" id="sp-overlay" onclick="closeSidebar()" aria-hidden="true"></div>

{{-- Main --}}
<div class="sp-main">
    {{-- Topbar --}}
    <header class="sp-topbar">
        <div style="display:flex;align-items:center;gap:1rem;">
            <button class="sp-btn sp-btn-ghost sp-btn-sm" id="sidebar-toggle"
                    onclick="toggleSidebar()" aria-label="Toggle menu"
                    style="display:none;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <span class="sp-page-title">{{ $title ?? 'Dashboard' }}</span>
        </div>

        <div style="display:flex;align-items:center;gap:0.75rem;">
            {{-- Theme Toggle Button --}}
            <button id="theme-toggle-btn" class="theme-toggle-btn" onclick="toggleTheme()" type="button" title="Ganti Tema (Gelap / Terang)" aria-label="Ganti Tema Tampilan">
                <span id="theme-toggle-icon" style="display:inline-flex;align-items:center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </span>
                <span id="theme-toggle-label">Tema Gelap</span>
            </button>

            {{-- Notifikasi badge (isu kritis) --}}
            @php $kritisCount = \App\Models\Issue::where('prioritas','kritis')->whereNotIn('status',['selesai','ditutup','hoaks'])->count(); @endphp
            @if($kritisCount > 0)
            <a href="{{ route('issues.index', ['prioritas' => 'kritis']) }}"
               class="sp-badge badge-kritis kritis-pulse"
               title="{{ $kritisCount }} isu kritis aktif">
                ⚡ {{ $kritisCount }} Kritis
            </a>
            @endif

            <span style="font-size:0.8rem;color:var(--clr-ink-3);">
                {{ now()->isoFormat('D MMM YYYY') }}
            </span>
        </div>
    </header>

    {{-- Alerts --}}
    <div style="padding: 0 2rem;">
        @if(session('success'))
        <div class="sp-alert sp-alert-success animate-fade-in" role="alert">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="sp-alert sp-alert-error animate-fade-in" role="alert">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
        @endif
    </div>

    {{-- Content --}}
    <main class="sp-content" id="main-content">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer style="padding: 1.25rem 2rem; border-top: 1px solid var(--clr-border); background: var(--clr-ground-2); font-size: 0.75rem; color: var(--clr-ink-3); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 18px; width: auto;">
            <span>&copy; {{ date('Y') }} Dinas Koperasi dan UKM, Perindustrian dan Perdagangan Provinsi Sulawesi Barat.</span>
        </div>
        <span style="color: #4caf50; font-weight: 500;">SIGAP PASAR SULBAR &middot; Rantai Komando Pangan Terpadu</span>
    </footer>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sp-sidebar');
        const overlay = document.getElementById('sp-overlay');
        sidebar.classList.toggle('open');
        overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
    }
    function closeSidebar() {
        const sidebar = document.getElementById('sp-sidebar');
        const overlay = document.getElementById('sp-overlay');
        sidebar.classList.remove('open');
        overlay.style.display = 'none';
    }

    // Show hamburger on mobile
    if (window.innerWidth <= 768) {
        document.getElementById('sidebar-toggle').style.display = 'flex';
    }
    window.addEventListener('resize', () => {
        const btn = document.getElementById('sidebar-toggle');
        btn.style.display = window.innerWidth <= 768 ? 'flex' : 'none';
        if (window.innerWidth > 768) closeSidebar();
    });

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.sp-alert').forEach(el => {
            el.style.transition = 'opacity 0.5s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 5000);

    // Theme Switcher Logic
    function applyThemeUI(theme) {
        const btn = document.getElementById('theme-toggle-btn');
        const icon = document.getElementById('theme-toggle-icon');
        const label = document.getElementById('theme-toggle-label');
        
        if (theme === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
            if (icon) {
                icon.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>';
            }
            if (label) label.textContent = 'Tema Terang';
            if (btn) btn.setAttribute('title', 'Beralih ke Tema Gelap');
        } else {
            document.documentElement.setAttribute('data-theme', 'dark');
            if (icon) {
                icon.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
            }
            if (label) label.textContent = 'Tema Gelap';
            if (btn) btn.setAttribute('title', 'Beralih ke Tema Terang');
        }
    }

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        try {
            localStorage.setItem('sigap_theme', newTheme);
        } catch (e) {}
        applyThemeUI(newTheme);
    }

    // Initialize UI on load based on active theme
    document.addEventListener('DOMContentLoaded', () => {
        const activeTheme = document.documentElement.getAttribute('data-theme') || (localStorage.getItem('sigap_theme') === 'dark' ? 'dark' : 'light');
        applyThemeUI(activeTheme);
    });
</script>
@stack('scripts')
</body>
</html>
