<x-app-layout>
    <x-slot name="title">Ringkasan Eksekutif Pimpinan</x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <style>
            .pimpinan-header-block {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 1.25rem;
                margin-bottom: 2rem;
                padding-bottom: 1.5rem;
                border-bottom: 1px solid var(--clr-border-2);
            }

            .filter-periode-pills {
                display: inline-flex;
                background: var(--clr-ground-2);
                border: 1px solid var(--clr-border-2);
                padding: 4px;
                border-radius: 10px;
                gap: 4px;
            }

            .periode-pill-btn {
                padding: 7px 16px;
                font-size: 0.85rem;
                font-weight: 600;
                border-radius: 7px;
                color: var(--clr-ink-2);
                text-decoration: none;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .periode-pill-btn:hover {
                color: var(--clr-ink);
                background: rgba(255, 255, 255, 0.05);
            }

            .periode-pill-btn.active {
                background: var(--clr-accent);
                color: #ffffff;
                box-shadow: 0 2px 8px rgba(46, 125, 50, 0.35);
            }

            .peta-sulbar-wrap {
                position: relative;
                height: 480px;
                width: 100%;
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid var(--clr-border-2);
                background: #111827;
                z-index: 1;
            }

            .map-kab-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 3px 8px;
                border-radius: 999px;
                font-size: 0.72rem;
                font-weight: 700;
            }

            .badge-kondusif {
                background: rgba(16, 185, 129, 0.15);
                color: #34d399;
                border: 1px solid rgba(16, 185, 129, 0.3);
            }

            .badge-waspada {
                background: rgba(245, 158, 11, 0.15);
                color: #fbbf24;
                border: 1px solid rgba(245, 158, 11, 0.3);
            }

            .badge-kritis {
                background: rgba(239, 68, 68, 0.15);
                color: #f87171;
                border: 1px solid rgba(239, 68, 68, 0.3);
            }

            .kab-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 1rem;
                margin-top: 1.25rem;
            }

            .kab-card-item {
                background: var(--clr-ground-3);
                border: 1px solid var(--clr-border);
                border-radius: 10px;
                padding: 14px 16px;
                cursor: pointer;
                transition: all 0.2s ease;
                position: relative;
            }

            .kab-card-item:hover {
                border-color: var(--clr-accent-3);
                transform: translateY(-2px);
            }

            .kab-card-item.active-kab-card {
                border-color: var(--clr-accent-3) !important;
                background: rgba(16, 185, 129, 0.08) !important;
                box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3), 0 4px 12px rgba(0, 0, 0, 0.15);
                transform: translateY(-2px);
            }

            .kab-card-item.active-kab-card.is-kritis {
                border-color: #ef4444 !important;
                background: rgba(239, 68, 68, 0.08) !important;
                box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.3), 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .kab-card-item.active-kab-card.is-waspada {
                border-color: #f59e0b !important;
                background: rgba(245, 158, 11, 0.08) !important;
                box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3), 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .kab-card-hint {
                font-size: 0.72rem;
                color: var(--clr-accent-3);
                margin-top: 8px;
                display: flex;
                align-items: center;
                gap: 4px;
                font-weight: 500;
            }

            .kab-detail-panel {
                animation: fadeInDetail 0.3s ease;
            }

            @keyframes fadeInDetail {
                from {
                    opacity: 0;
                    transform: translateY(6px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @media (max-width: 900px) {
                .kab-detail-body-grid {
                    grid-template-columns: 1fr !important;
                }
            }

            .leaflet-popup-content-wrapper {
                background: #1e293b !important;
                color: #f1f5f9 !important;
                border-radius: 10px !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
            }

            .leaflet-popup-tip {
                background: #1e293b !important;
            }

            .chart-box {
                position: relative;
                width: 100%;
                height: 280px;
            }

            .price-progress-bar {
                height: 6px;
                background: rgba(255, 255, 255, 0.08);
                border-radius: 3px;
                overflow: hidden;
                position: relative;
                margin-top: 6px;
            }

            .price-progress-fill {
                height: 100%;
                border-radius: 3px;
                transition: width 0.4s ease;
            }
        </style>
    @endpush

    {{-- Filter Periode & Waktu Analisis --}}
    <span style="display:none;" aria-hidden="true">Dashboard Analitik</span>
    <div class="pimpinan-header-block">
        <div style="display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;">
            <div class="filter-periode-pills" role="tablist" aria-label="Filter Periode Analisis">
                <a href="{{ route('dashboard', ['periode' => 'hari']) }}"
                    class="periode-pill-btn {{ $periode === 'hari' ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    Hari Ini
                </a>
                <a href="{{ route('dashboard', ['periode' => 'minggu']) }}"
                    class="periode-pill-btn {{ $periode === 'minggu' ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    7 Hari Terakhir
                </a>
                <a href="{{ route('dashboard', ['periode' => 'bulan']) }}"
                    class="periode-pill-btn {{ $periode === 'bulan' ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    Bulan Ini
                </a>
                <a href="{{ route('dashboard', ['periode' => 'tahun']) }}"
                    class="periode-pill-btn {{ $periode === 'tahun' ? 'active' : '' }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <line x1="18" y1="20" x2="18" y2="10" />
                        <line x1="12" y1="20" x2="12" y2="4" />
                        <line x1="6" y1="20" x2="6" y2="14" />
                    </svg>
                    Tahun Ini
                </a>
            </div>

            <div
                style="font-size:0.85rem;color:var(--clr-ink-3);font-weight:600;display:inline-flex;align-items:center;gap:0.4rem;">
                PERIODE DATA: <span style="color:var(--clr-accent-3);font-weight:700;">{{ $periodeLabel }}</span>
            </div>
        </div>
    </div>

    {{-- Alert Info Jika Ada --}}
    @if (session('info'))
        <div
            style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3);color:#93c5fd;padding:12px 18px;border-radius:8px;margin-bottom:1.5rem;font-size:0.9rem;">
            {{ session('info') }}
        </div>
    @endif

    {{-- Ringkasan Eksekutif Stabilitas Pangan (Executive Briefing) --}}
    <div class="sp-card sp-executive-briefing"
        style="margin-bottom:1.75rem;border-left:4px solid {{ $statusDaerahLevel === 'kritis' ? 'var(--clr-kritis)' : ($statusDaerahLevel === 'waspada' ? 'var(--clr-intervensi)' : 'var(--clr-selesai)') }};">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1.25rem;">
            <div style="display:flex;align-items:center;gap:1rem;flex:1;min-width:300px;">
                <div
                    style="width:48px;height:48px;border-radius:12px;background:{{ $statusDaerahLevel === 'kritis' ? 'rgba(239,68,68,0.15)' : ($statusDaerahLevel === 'waspada' ? 'rgba(245,158,11,0.15)' : 'rgba(16,185,129,0.15)') }};display:flex;align-items:center;justify-content:center;color:{{ $statusDaerahLevel === 'kritis' ? '#ef4444' : ($statusDaerahLevel === 'waspada' ? '#f59e0b' : '#10b981') }};flex-shrink:0;">
                    @if ($statusDaerahLevel === 'kritis')
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                    @elseif($statusDaerahLevel === 'waspada')
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                    @else
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            <polyline points="9 12 11 14 15 10" />
                        </svg>
                    @endif
                </div>
                <div>
                    <div style="margin-bottom:6px;">
                        <span
                            class="sp-badge {{ $statusDaerahLevel === 'kritis' ? 'badge-kritis' : ($statusDaerahLevel === 'waspada' ? 'badge-intervensi' : 'badge-selesai') }}"
                            style="font-size:0.75rem;padding:0.25rem 0.65rem;">
                            {{ $statusDaerahLabel }}
                        </span>
                    </div>
                    <div style="font-size:0.95rem;color:var(--clr-ink);font-weight:600;line-height:1.45;">
                        {!! nl2br(e($ringkasanEksekutif)) !!}
                    </div>
                </div>
            </div>

            {{-- Indikator Stabilitas Komoditas --}}
            <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
                <div class="kpi-mini-box kpi-stabil"
                    style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);padding:8px 14px;border-radius:8px;text-align:center;min-width:105px;">
                    <div class="kpi-mini-val" style="font-size:1.3rem;font-weight:700;color:#34d399;">
                        {{ $komoditasStabilCount }}</div>
                    <div class="kpi-mini-lbl"
                        style="font-size:0.7rem;color:#a7f3d0;text-transform:uppercase;font-weight:700;">Stabil (HET)
                    </div>
                </div>
                <div class="kpi-mini-box kpi-waspada"
                    style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.25);padding:8px 14px;border-radius:8px;text-align:center;min-width:105px;">
                    <div class="kpi-mini-val" style="font-size:1.3rem;font-weight:700;color:#fbbf24;">
                        {{ $komoditasWaspadaCount }}</div>
                    <div class="kpi-mini-lbl"
                        style="font-size:0.7rem;color:#fde68a;text-transform:uppercase;font-weight:700;">Waspada
                        (5-15%)</div>
                </div>
                <div class="kpi-mini-box kpi-kritis"
                    style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);padding:8px 14px;border-radius:8px;text-align:center;min-width:105px;">
                    <div class="kpi-mini-val" style="font-size:1.3rem;font-weight:700;color:#f87171;">
                        {{ $komoditasKritisCount }}</div>
                    <div class="kpi-mini-lbl"
                        style="font-size:0.7rem;color:#fecaca;text-transform:uppercase;font-weight:700;">Melambung
                        (&gt;15%)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Executive Summary KPI Cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1rem;margin-bottom:2rem;">
        {{-- Total Isu --}}
        <div class="sp-stat-card" style="border-left:3px solid var(--clr-baru);">
            <span class="sp-stat-label">Total Isu Masuk</span>
            <div class="sp-stat-value" style="color:var(--clr-baru);">{{ $totalIsu }}</div>
            <span class="sp-stat-sub">Periode {{ $periodeLabel }}</span>
        </div>

        {{-- Isu Kritis --}}
        <div class="sp-stat-card" style="border-left:3px solid var(--clr-kritis);">
            <span class="sp-stat-label">Prioritas Kritis / Urgent</span>
            <div class="sp-stat-value" style="color:var(--clr-kritis);">{{ $isuKritis }}</div>
            <span class="sp-stat-sub">Memerlukan atensi pimpinan</span>
        </div>

        {{-- Sedang Diproses --}}
        <div class="sp-stat-card" style="border-left:3px solid var(--clr-ditugaskan);">
            <span class="sp-stat-label">Sedang Dalam Penanganan</span>
            <div class="sp-stat-value" style="color:var(--clr-ditugaskan);">{{ $isuDiproses }}</div>
            <span class="sp-stat-sub">Verifikasi, Lapangan & Intervensi</span>
        </div>

        {{-- Selesai Ditangani --}}
        <div class="sp-stat-card" style="border-left:3px solid var(--clr-selesai);">
            <span class="sp-stat-label">Berhasil Dituntaskan</span>
            <div class="sp-stat-value" style="color:var(--clr-selesai);">{{ $isuSelesai }}</div>
            <span class="sp-stat-sub">Tingkat Penyelesaian: {{ $rasioSelesai }}%</span>
        </div>
    </div>


    {{-- BAGIAN 1: PETA SEBARAN TITIK ISU SULAWESI BARAT --}}
    <div class="sp-card" style="margin-bottom:2rem;">
        <div
            style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <h2 style="font-size:1.2rem;font-weight:700;margin:0;letter-spacing:-0.02em;">
                    Peta Sebaran Titik Isu & Status Kerawanan 6 Kabupaten
                </h2>
            </div>

            <div style="display:flex;align-items:center;gap:12px;font-size:0.75rem;">
                <span style="display:inline-flex;align-items:center;gap:5px;color:#f87171;">
                    <span
                        style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                    Kritis
                </span>
                <span style="display:inline-flex;align-items:center;gap:5px;color:#fbbf24;">
                    <span
                        style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                    Waspada
                </span>
                <span style="display:inline-flex;align-items:center;gap:5px;color:#34d399;">
                    <span
                        style="width:10px;height:10px;border-radius:50%;background:#10b981;display:inline-block;"></span>
                    Kondusif
                </span>
            </div>
        </div>

        {{-- Map Container --}}
        <div id="petaSulbar" class="peta-sulbar-wrap" role="region" aria-label="Peta Sulawesi Barat"></div>

        {{-- List Kabupaten Cards --}}
        <div class="kab-grid">
            @foreach ($petaKabupaten as $kab)
                <div id="kab-card-{{ $kab['id'] }}"
                    class="kab-card-item {{ $kab['status_level'] === 'kritis' ? 'is-kritis' : ($kab['status_level'] === 'waspada' ? 'is-waspada' : 'is-kondusif') }}"
                    onclick="toggleKabupaten('{{ $kab['name'] }}')">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <strong style="color:var(--clr-ink);font-size:0.95rem;">{{ $kab['name'] }}</strong>
                        <span class="map-kab-badge badge-{{ $kab['status_level'] }}">
                            {{ strtoupper($kab['status_level']) }}
                        </span>
                    </div>
                    <div
                        style="display:flex;align-items:center;gap:0.75rem;font-size:0.8rem;color:var(--clr-ink-2);margin-bottom:6px;flex-wrap:wrap;">
                        <span>Aktif: <strong
                                style="color:{{ $kab['isu_aktif'] > 0 ? 'var(--clr-kritis)' : 'var(--clr-selesai)' }};">{{ $kab['isu_aktif'] }}</strong></span>
                        <span>&bull;</span>
                        <span>Total: <strong
                                style="color:var(--clr-ink);">{{ $kab['total_isu_periode'] }}</strong></span>
                        <span>&bull;</span>
                        <span>Selesai: <strong
                                style="color:var(--clr-selesai);">{{ $kab['isu_selesai'] }}</strong></span>
                    </div>
                    <div
                        style="font-size:0.75rem;color:var(--clr-ink-3);display:flex;justify-content:space-between;align-items:center;">
                        <span>Komoditas: <span
                                style="color:var(--clr-accent-3);font-weight:600;">{{ $kab['komoditas_utama'] }}</span></span>
                    </div>
                    <div class="kab-card-hint">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <span>Klik untuk fokus &bull; Tekan lagi untuk semua data</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- PANEL DATA LENGKAP KABUPATEN TERPILIH --}}
        <div id="detailKabupatenCard" class="kab-detail-panel"
            style="margin-top:1.75rem;border-top:1px solid var(--clr-border);padding-top:1.5rem;">
            {{-- Header Detail (Hanya tampil saat fokus ke kabupaten) --}}
            <div id="detKabHeader"
                style="display:none;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.25rem;">
                <div>
                    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.35rem;flex-wrap:wrap;">
                        <h3 id="detKabName"
                            style="font-size:1.25rem;font-weight:700;margin:0;color:var(--clr-ink);letter-spacing:-0.02em;">
                        </h3>
                        <span id="detKabBadge" class="map-kab-badge badge-kondusif"></span>
                        <span id="detKabCoord" style="font-size:0.75rem;color:var(--clr-ink-3);"></span>
                    </div>
                </div>
            </div>

            {{-- KPI Mini Bar (Hanya tampil saat fokus ke kabupaten) --}}
            <div id="detKpiGrid"
                style="display:none;grid-template-columns:repeat(auto-fit, minmax(130px, 1fr));gap:0.75rem;margin-bottom:1.5rem;">
                {{-- Populated via JS --}}
            </div>

            {{-- Daftar Isu Terkini di Kabupaten (Full Width) --}}
            <div class="sp-card"
                style="padding:1.25rem;background:var(--clr-ground-2);border:1px solid var(--clr-border);width:100%;">
                <div
                    style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.75rem;">
                    <h4
                        style="margin:0;font-size:0.95rem;font-weight:700;display:flex;align-items:center;gap:0.5rem;color:var(--clr-ink);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <span id="detIsuTableTitle">Daftar Isu Terkini di Seluruh Wilayah</span>
                    </h4>
                    <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
                        <span id="detIsuCountBadge" class="sp-badge badge-baru" style="font-size:0.72rem;">0 Isu</span>
                        <a id="detBtnAllIssues" href="#" class="sp-btn sp-btn-primary sp-btn-sm"
                            style="font-size:0.78rem;gap:6px;padding:4px 12px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                                <polyline points="10 9 9 9 8 9" />
                            </svg>
                            Lihat Detail
                        </a>
                    </div>
                </div>
                <div id="detIsuTableWrap">
                    {{-- Populated via JS --}}
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: GRAFIK TREN PERIODE & ANALITIK WAKTU --}}
    <div
        style="display:grid;grid-template-columns:repeat(auto-fit, minmax(360px, 1fr));gap:1.5rem;margin-bottom:2rem;">
        {{-- Grafik Tren Harga Komoditas Harian --}}
        <div class="sp-card">
            <div style="margin-bottom:1.25rem;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:0.75rem;">
                    <div>
                        <h3 style="font-size:1.1rem;font-weight:700;margin:0 0 0.25rem;color:var(--clr-ink);">
                            Tren Harga Komoditas Harian ({{ $periodeLabel }})
                        </h3>
                        <p style="color:var(--clr-ink-3);font-size:0.8rem;margin:0;">
                            Perkembangan harga harian komoditas hasil upload file excel data pasar harian vs batas acuan HET.
                        </p>
                    </div>
                    @if(!empty($grafikTren['latest_upload_info']))
                    <span class="sp-badge badge-selesai" style="font-size:0.72rem;padding:0.25rem 0.65rem;">
                        {{ $grafikTren['latest_upload_info'] }}
                    </span>
                    @endif
                </div>

                {{-- Filter Pilihan Komoditas Grafik (Icon SVG Tanpa Emotikon) --}}
                <div style="display:flex;align-items:center;gap:0.6rem;margin-top:0.85rem;flex-wrap:wrap;">
                    {{-- Tombol Cepat Ber-icon SVG --}}
                    <div style="display:inline-flex;align-items:center;gap:0.35rem;flex-wrap:wrap;">
                        <button type="button" id="btnFilterDisparitas" class="sp-btn sp-btn-sm sp-btn-primary"
                            style="font-size:0.78rem;padding:0.3rem 0.65rem;display:inline-flex;align-items:center;gap:0.35rem;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                            <span>Disparitas Terbesar</span>
                        </button>
                        <button type="button" id="btnFilterAll" class="sp-btn sp-btn-sm sp-btn-secondary"
                            style="font-size:0.78rem;padding:0.3rem 0.65rem;display:inline-flex;align-items:center;gap:0.35rem;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="20" x2="18" y2="10"/>
                                <line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                            <span>Semua Komoditas</span>
                        </button>
                    </div>

                    {{-- Dropdown Pilihan Komoditas dengan Icon SVG (Murni Teks Tanpa Emotikon) --}}
                    <div style="position:relative;display:inline-flex;align-items:center;">
                        <span style="position:absolute;left:0.65rem;pointer-events:none;color:var(--clr-ink-3);display:flex;align-items:center;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </span>
                        <select id="filterKomoditasChart" class="sp-select" style="padding:0.35rem 0.75rem 0.35rem 1.95rem;font-size:0.8rem;font-weight:600;min-width:240px;border-color:var(--clr-border);">
                            <option value="disparitas">Disparitas Terbesar vs HET (Default)</option>
                            <option value="all">Tampilkan Semua Komoditas Terdata</option>
                            <optgroup label="Pilih Komoditas Spesifik:">
                                @foreach($grafikTren['all_komoditas'] ?? [] as $kom)
                                    <option value="{{ $kom['id'] }}">{{ $kom['name'] }} (HET: Rp {{ number_format($kom['het'], 0, ',', '.') }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <span id="chartFilterBadge" class="sp-badge badge-baru" style="font-size:0.72rem;padding:0.25rem 0.65rem;display:inline-flex;align-items:center;gap:0.35rem;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                        </svg>
                        <span id="chartFilterBadgeText">Disparitas &amp; Isu</span>
                    </span>
                </div>
            </div>
            <div class="chart-box">
                <canvas id="chartHarga"></canvas>
            </div>
        </div>

        {{-- Grafik Komoditas Paling Berfluktuasi --}}
        <div class="sp-card">
            <div style="margin-bottom:1.25rem;">
                <h3 style="font-size:1.1rem;font-weight:700;margin:0 0 0.25rem;color:var(--clr-ink);">
                    Disparitas Harga Terbesar vs HET
                </h3>
            </div>
            <div style="display:flex;flex-direction:column;gap:1.25rem;padding-top:0.5rem;">
                @forelse($grafikTren['top_komoditas'] as $topK)
                    <div>
                        <div
                            style="display:flex;justify-content:space-between;align-items:center;font-size:0.88rem;margin-bottom:4px;">
                            <span style="font-weight:600;color:var(--clr-ink);display:inline-flex;align-items:center;gap:0.4rem;">
                                {{ $topK['name'] }}
                                @if(!empty($topK['isu_count']) && $topK['isu_count'] > 0)
                                    <span class="sp-badge badge-kritis" style="font-size:0.68rem;padding:0.12rem 0.45rem;">
                                        {{ $topK['isu_count'] }} Isu
                                    </span>
                                @endif
                            </span>
                            <span class="sp-badge {{ $topK['status_class'] }}" style="font-size:0.75rem;">
                                {{ $topK['persen_selisih'] > 0 ? '+' : '' }}{{ $topK['persen_selisih'] }}%
                            </span>
                        </div>
                        <div
                            style="display:flex;justify-content:space-between;font-size:0.78rem;color:var(--clr-ink-3);margin-bottom:6px;">
                            <span>HET: Rp {{ number_format($topK['het'], 0, ',', '.') }}/{{ $topK['satuan'] }}</span>
                            <span style="color:var(--clr-ink-2);font-weight:600;">Aktual: Rp
                                {{ number_format($topK['harga_aktual'], 0, ',', '.') }}</span>
                        </div>
                        <div class="price-progress-bar">
                            @php
                                $fillPct = min(100, max(10, 100 + $topK['persen_selisih']));
                                $barColor =
                                    $topK['persen_selisih'] > 15
                                        ? '#ef4444'
                                        : ($topK['persen_selisih'] > 5
                                            ? '#f59e0b'
                                            : '#10b981');
                            @endphp
                            <div class="price-progress-fill"
                                style="width: {{ $fillPct }}%; background: {{ $barColor }};"></div>
                        </div>
                    </div>
                @empty
                    <div style="color:var(--clr-ink-3);text-align:center;padding:2rem 0;font-size:0.85rem;">
                        Belum ada komoditas dengan selisih harga signifikan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- BAGIAN 3: TABEL PERKEMBANGAN HARGA KOMODITAS POKOK VS HET --}}
    <div class="sp-card">
        <div
            style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.25rem;">
                    <span class="sp-badge badge-selesai">Stabilisasi Harga</span>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <div style="font-size:0.82rem;color:var(--clr-ink-3);">
                    Total Komoditas: <strong style="color:var(--clr-ink);">{{ count($perkembanganHarga) }} Komoditas
                        Terpantau</strong>
                </div>
                <a href="{{ route('prices.index') }}" class="sp-btn sp-btn-secondary sp-btn-sm"
                    style="font-size:0.78rem;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="12" y1="18" x2="12" y2="12" />
                        <line x1="9" y1="15" x2="15" y2="15" />
                    </svg>
                    Lihat Rincian Seluruh Varian &rarr;
                </a>
            </div>
        </div>

        <div class="sp-table-wrap">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th style="min-width:200px;">Komoditas Pokok</th>
                        <th style="width:100px;">Satuan</th>
                        <th style="text-align:right;min-width:140px;">Harga Acuan (HET)</th>
                        <th style="text-align:right;min-width:160px;">Harga Pasar Terkini</th>
                        <th style="text-align:right;min-width:160px;">Selisih vs HET</th>
                        <th style="text-align:center;min-width:150px;">Status Ketahanan</th>
                        <th style="text-align:center;min-width:100px;">Isu Terkait</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($perkembanganHarga as $item)
                        <tr>
                            <td class="cell-primary">
                                <strong style="color:var(--clr-ink);font-size:0.95rem;">{{ $item['name'] }}</strong>
                                <div style="font-size:0.75rem;color:var(--clr-ink-3);">Kode: {{ $item['code'] }}</div>
                            </td>
                            <td style="font-size:0.85rem;color:var(--clr-ink-2);">{{ $item['satuan'] }}</td>
                            <td style="text-align:right;font-weight:600;color:var(--clr-ink-2);font-size:0.9rem;">
                                Rp {{ number_format($item['het'], 0, ',', '.') }}
                            </td>
                            <td style="text-align:right;font-weight:700;color:var(--clr-ink);font-size:0.95rem;">
                                Rp {{ number_format($item['harga_aktual'], 0, ',', '.') }}
                                <span
                                    style="font-size:0.68rem;color:var(--clr-ink-3);display:block;font-weight:normal;margin-top:2px;">
                                    @if (($item['sumber_harga'] ?? '') === 'Import Pasar')
                                        <span style="color:#34d399;">&bull; Data Pasar</span>
                                        @if (($item['variants_count'] ?? 0) > 1)
                                            ({{ $item['variants_count'] }} varian)
                                        @endif
                                    @elseif(($item['sumber_harga'] ?? '') === 'Inspeksi Lapangan')
                                        <span style="color:#38bdf8;">&bull; Inspeksi Lapangan</span>
                                    @else
                                        &bull; Acuan Master
                                    @endif
                                </span>
                            </td>
                            <td style="text-align:right;font-weight:700;font-size:0.9rem;">
                                @if ($item['selisih'] > 0)
                                    <span style="color:#f87171;">+Rp
                                        {{ number_format($item['selisih'], 0, ',', '.') }}
                                        (+{{ $item['persen_selisih'] }}%)</span>
                                @elseif($item['selisih'] < 0)
                                    <span style="color:#34d399;">-Rp
                                        {{ number_format(abs($item['selisih']), 0, ',', '.') }}
                                        ({{ $item['persen_selisih'] }}%)</span>
                                @else
                                    <span style="color:var(--clr-ink-3);">Rp 0 (0%)</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <span class="sp-badge {{ $item['status_class'] }}">
                                    {{ $item['status_harga'] }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                @if ($item['isu_count'] > 0)
                                    <span
                                        style="font-weight:700;color:var(--clr-kritis);font-size:0.85rem;">{{ $item['isu_count'] }}
                                        isu</span>
                                @else
                                    <span style="color:var(--clr-ink-3);font-size:0.8rem;">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:2.5rem 0;color:var(--clr-ink-3);">
                                Belum ada master komoditas yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        {{-- Leaflet JS & Chart.js via reliable CDNs --}}
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ─── 1. Inisialisasi Peta Leaflet Sulawesi Barat ───
                const sulbarCenter = [-2.85, 119.15];
                const map = L.map('petaSulbar', {
                    center: sulbarCenter,
                    zoom: 8,
                    zoomControl: true,
                    scrollWheelZoom: false
                });

                // Basemap OpenStreetMap (100% Gratis & Bebas API Key)
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" style="color:#38bdf8;">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(map);

                // Data titik kabupaten & seluruh wilayah dari server
                const petaKabupaten = @json($petaKabupaten);
                const semuaWilayahData = @json($semuaWilayahData);
                const titikLapangan = @json($titikLapangan);

                // Simpan marker dalam objek untuk interaksi klik kartu
                window.kabMarkers = {};
                window.currentSelectedKab = null;

                // Render Titik Kabupaten
                petaKabupaten.forEach(function(kab) {
                    let markerColor = '#10b981';
                    if (kab.status_level === 'kritis') markerColor = '#ef4444';
                    else if (kab.status_level === 'waspada') markerColor = '#f59e0b';

                    const circleMarker = L.circleMarker([kab.lat, kab.lng], {
                        radius: 12,
                        fillColor: markerColor,
                        color: '#ffffff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.85
                    }).addTo(map);

                    const popupHtml = `
                    <div style="font-family: 'Poppins', system-ui, sans-serif; padding: 4px; min-width: 180px;">
                        <h4 style="margin: 0 0 6px; font-size: 14px; font-weight: 700; color: #f8fafc;">${kab.name}</h4>
                        <div style="font-size: 12px; margin-bottom: 4px; color: #cbd5e1;">Status: <b style="color:${markerColor}; text-transform:uppercase;">${kab.status_level}</b></div>
                        <div style="font-size: 12px; margin-bottom: 4px; color: #cbd5e1;">Isu Aktif: <b>${kab.isu_aktif}</b> (Total: ${kab.total_isu_periode})</div>
                        <div style="font-size: 11px; color: #94a3b8;">Komoditas: <b>${kab.komoditas_utama}</b></div>
                    </div>
                `;
                    circleMarker.bindPopup(popupHtml);
                    circleMarker.on('click', function() {
                        window.toggleKabupaten(kab.name);
                    });
                    window.kabMarkers[kab.name] = {
                        marker: circleMarker,
                        lat: kab.lat,
                        lng: kab.lng
                    };
                });

                // Render Titik Lapangan Cek Fisik (Jika Ada)
                titikLapangan.forEach(function(tp) {
                    const pin = L.circleMarker([tp.lat, tp.lng], {
                        radius: 6,
                        fillColor: '#06b6d4',
                        color: '#ffffff',
                        weight: 1.5,
                        opacity: 1,
                        fillOpacity: 0.9
                    }).addTo(map);

                    const pinHtml = `
                    <div style="font-family: 'Poppins', system-ui, sans-serif; padding: 4px; min-width: 170px;">
                        <div style="font-size: 10px; font-weight: 700; color: #38bdf8; text-transform:uppercase;">Inspeksi Lapangan</div>
                        <h5 style="margin: 2px 0 4px; font-size: 13px; font-weight: 700; color: #ffffff;">${tp.komoditas}</h5>
                        <div style="font-size: 11px; color: #e2e8f0;">Lokasi: ${tp.lokasi}</div>
                        <div style="font-size: 12px; font-weight: 700; color: #f59e0b; margin-top: 3px;">
                            Harga: Rp ${Number(tp.harga_aktual).toLocaleString('id-ID')}
                        </div>
                        <div style="font-size: 10px; color: #94a3b8; margin-top: 3px;">
                            Petugas: ${tp.petugas} &bull; ${tp.tanggal}
                        </div>
                    </div>
                `;
                    pin.bindPopup(pinHtml);
                });

                // ─── Helper Render Tabel Isu Terkini ───
                function renderIsuTable(isuList, wilayahLabel) {
                    const isuWrap = document.getElementById('detIsuTableWrap');
                    if (isuList && isuList.length > 0) {
                        let htmlIsu = `
                        <div class="sp-table-wrap" style="border:none;background:transparent;overflow-x:auto;">
                            <table class="sp-table" style="font-size:0.85rem;width:100%;">
                                <thead>
                                    <tr>
                                        <th style="min-width:140px;">Kode Isu</th>
                                        <th style="min-width:180px;">Komoditas &amp; Lokasi</th>
                                        <th style="text-align:center;min-width:90px;">Virality</th>
                                        <th style="text-align:center;min-width:110px;">Prioritas</th>
                                        <th style="text-align:center;min-width:130px;">Status Alur</th>
                                        <th style="min-width:100px;">Tanggal Temuan</th>
                                        <th style="text-align:right;min-width:120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                        isuList.forEach(isu => {
                            htmlIsu += `
                            <tr>
                                <td class="cell-code" style="white-space:nowrap;">
                                    <a href="${isu.url}" style="color:var(--clr-accent-3);font-weight:700;">${isu.kode_isu}</a>
                                </td>
                                <td>
                                    <strong style="color:var(--clr-ink);font-size:0.9rem;">${isu.komoditas}</strong><br>
                                    <span style="font-size:0.75rem;color:var(--clr-ink-3);">${isu.lokasi}</span>
                                </td>
                                <td style="text-align:center;">
                                    <span style="font-weight:700;color:var(--clr-ink);font-size:0.85rem;">${isu.virality_score}</span>
                                </td>
                                <td style="text-align:center;">
                                    <span class="sp-badge badge-${isu.prioritas}" style="font-size:0.72rem;">${isu.prioritas_label}</span>
                                </td>
                                <td style="text-align:center;">
                                    <span class="sp-badge badge-${isu.status}" style="font-size:0.72rem;">${isu.status_label}</span>
                                </td>
                                <td style="font-size:0.78rem;color:var(--clr-ink-3);white-space:nowrap;">
                                    ${isu.tanggal}
                                </td>
                                <td style="text-align:right;white-space:nowrap;">
                                    <a href="${isu.url}" class="sp-btn sp-btn-ghost sp-btn-sm" style="font-size:0.75rem;padding:4px 10px;">
                                        Lihat Progres &rarr;
                                    </a>
                                </td>
                            </tr>
                        `;
                        });
                        htmlIsu += `</tbody></table></div>`;
                        isuWrap.innerHTML = htmlIsu;
                    } else {
                        isuWrap.innerHTML = `
                        <div style="text-align:center;padding:2.5rem 1rem;color:var(--clr-ink-3);">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 0.75rem;opacity:0.4;"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                            <div style="font-size:0.92rem;font-weight:600;color:var(--clr-ink-2);margin-bottom:0.25rem;">Tidak Ada Isu Masuk</div>
                            <div style="font-size:0.8rem;">Kondisi stabilitas pasokan dan harga di ${wilayahLabel} terpantau stabil dan kondusif.</div>
                        </div>
                    `;
                    }
                }

                // ─── Fungsi Reset Ke Semua Data Wilayah Sulawesi Barat ───
                window.resetKeSemuaWilayah = function() {
                    window.currentSelectedKab = null;

                    // 1. Reset Peta ke Tampilan Penuh Sulbar
                    map.flyTo(sulbarCenter, 8, {
                        duration: 1.2
                    });
                    map.closePopup();

                    // 2. Bersihkan Class Active dari semua kartu kabupaten
                    document.querySelectorAll('.kab-card-item').forEach(el => el.classList.remove(
                        'active-kab-card'));

                    // 3. Sembunyikan Header Detail & Kosongkan KPI saat semua wilayah
                    const detHeader = document.getElementById('detKabHeader');
                    if (detHeader) {
                        detHeader.style.display = 'none';
                    }
                    const detName = document.getElementById('detKabName');
                    if (detName) {
                        detName.textContent = '';
                    }
                    const badgeEl = document.getElementById('detKabBadge');
                    if (badgeEl) {
                        badgeEl.textContent = '';
                    }
                    document.getElementById('detKabCoord').textContent = '';

                    const kpiGrid = document.getElementById('detKpiGrid');
                    if (kpiGrid) {
                        kpiGrid.style.display = 'none';
                        kpiGrid.innerHTML = '';
                    }

                    // Tombol Aksi
                    const allIssuesBtn = document.getElementById('detBtnAllIssues');
                    if (allIssuesBtn) {
                        allIssuesBtn.href = semuaWilayahData.link_semua_isu;
                        allIssuesBtn.innerHTML = `
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        Lihat Detail
                    `;
                    }



                    // 5. Render Daftar Isu Terkini Lintas Wilayah
                    const tableTitle = document.getElementById('detIsuTableTitle');
                    if (tableTitle) {
                        tableTitle.textContent = 'Daftar Isu Terkini di Seluruh Sulawesi Barat';
                    }
                    const isuCountBadge = document.getElementById('detIsuCountBadge');
                    isuCountBadge.textContent =
                        `${semuaWilayahData.isu_terkini ? semuaWilayahData.isu_terkini.length : 0} Isu Terdata`;
                    renderIsuTable(semuaWilayahData.isu_terkini, 'Sulawesi Barat');
                };

                // ─── Fungsi Memilih Kabupaten Tertentu (Fokus) ───
                window.selectKabupaten = function(name, shouldZoom = true) {
                    const kab = petaKabupaten.find(k => k.name === name);
                    if (!kab) return;

                    window.currentSelectedKab = kab;

                    // 1. Auto Fokus / Zoom Peta
                    if (shouldZoom) {
                        map.flyTo([kab.lat, kab.lng], 11, {
                            duration: 1.2
                        });
                        if (window.kabMarkers[name]) {
                            window.kabMarkers[name].marker.openPopup();
                        }
                    }

                    // 2. Active Class pada Kartu Kabupaten
                    document.querySelectorAll('.kab-card-item').forEach(el => el.classList.remove(
                        'active-kab-card'));
                    const activeCard = document.getElementById(`kab-card-${kab.id}`);
                    if (activeCard) {
                        activeCard.classList.add('active-kab-card');
                    }

                    // 3. Render Header Panel Detail Kabupaten
                    const detHeader = document.getElementById('detKabHeader');
                    if (detHeader) {
                        detHeader.style.display = 'flex';
                    }
                    const detName = document.getElementById('detKabName');
                    if (detName) {
                        detName.textContent = kab.name;
                    }
                    const badgeEl = document.getElementById('detKabBadge');
                    if (badgeEl) {
                        badgeEl.className = `map-kab-badge badge-${kab.status_level}`;
                        badgeEl.textContent = kab.status_level.toUpperCase();
                    }
                    document.getElementById('detKabCoord').textContent =
                        `Koordinat: ${kab.lat.toFixed(4)}, ${kab.lng.toFixed(4)}`;

                    // 4. Render KPI Mini Bar
                    const kpiGrid = document.getElementById('detKpiGrid');
                    if (kpiGrid) {
                        kpiGrid.style.display = 'grid';
                        kpiGrid.innerHTML = `
                    <div class="sp-stat-card" style="padding:10px 14px;border-left:3px solid var(--clr-baru);">
                        <span class="sp-stat-label" style="font-size:0.7rem;">Isu Masuk (Periode)</span>
                        <div class="sp-stat-value" style="font-size:1.35rem;color:var(--clr-baru);">${kab.total_isu_periode}</div>
                        <span class="sp-stat-sub" style="font-size:0.68rem;">Total Riwayat: ${kab.total_isu_all}</span>
                    </div>
                    <div class="sp-stat-card" style="padding:10px 14px;border-left:3px solid ${kab.isu_aktif > 0 ? 'var(--clr-kritis)' : 'var(--clr-selesai)'};">
                        <span class="sp-stat-label" style="font-size:0.7rem;">Isu Aktif Berjalan</span>
                        <div class="sp-stat-value" style="font-size:1.35rem;color:${kab.isu_aktif > 0 ? 'var(--clr-kritis)' : 'var(--clr-selesai)'};">${kab.isu_aktif}</div>
                        <span class="sp-stat-sub" style="font-size:0.68rem;">Belum Tuntas</span>
                    </div>
                    <div class="sp-stat-card" style="padding:10px 14px;border-left:3px solid var(--clr-kritis);">
                        <span class="sp-stat-label" style="font-size:0.7rem;">Prioritas Kritis</span>
                        <div class="sp-stat-value" style="font-size:1.35rem;color:var(--clr-kritis);">${kab.isu_kritis}</div>
                        <span class="sp-stat-sub" style="font-size:0.68rem;">Butuh Atensi</span>
                    </div>
                    <div class="sp-stat-card" style="padding:10px 14px;border-left:3px solid var(--clr-intervensi);">
                        <span class="sp-stat-label" style="font-size:0.7rem;">Intervensi / Lapangan</span>
                        <div class="sp-stat-value" style="font-size:1.35rem;color:var(--clr-intervensi);">${kab.isu_intervensi + kab.isu_lapangan}</div>
                        <span class="sp-stat-sub" style="font-size:0.68rem;">Dalam Proses Aksi</span>
                    </div>
                    <div class="sp-stat-card" style="padding:10px 14px;border-left:3px solid var(--clr-selesai);">
                        <span class="sp-stat-label" style="font-size:0.7rem;">Selesai Ditangani</span>
                        <div class="sp-stat-value" style="font-size:1.35rem;color:var(--clr-selesai);">${kab.isu_selesai}</div>
                        <span class="sp-stat-sub" style="font-size:0.68rem;">Rasio: ${kab.rasio_selesai}%</span>
                    </div>
                `;
                    }

                    // Tombol Aksi
                    const allIssuesBtn = document.getElementById('detBtnAllIssues');
                    if (allIssuesBtn) {
                        allIssuesBtn.href = kab.link_semua_isu;
                        allIssuesBtn.innerHTML = `
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        Lihat Detail
                    `;
                    }

                    // 5. Render Daftar Isu Terkini
                    const tableTitle = document.getElementById('detIsuTableTitle');
                    if (tableTitle) {
                        tableTitle.textContent = `Daftar Isu Terkini di ${kab.name}`;
                    }
                    const isuCountBadge = document.getElementById('detIsuCountBadge');
                    isuCountBadge.textContent = `${kab.isu_terkini ? kab.isu_terkini.length : 0} Isu Terdata`;
                    renderIsuTable(kab.isu_terkini, kab.name);
                };

                // ─── Fungsi Toggle: Klik 1x Fokus Kabupaten, Klik 2x Kembali ke Semua Data ───
                window.toggleKabupaten = function(name) {
                    if (window.currentSelectedKab && window.currentSelectedKab.name === name) {
                        window.resetKeSemuaWilayah();
                    } else {
                        window.selectKabupaten(name, true);
                    }
                };

                // Alias kompatibilitas
                window.focusKabupaten = function(lat, lng, name) {
                    window.toggleKabupaten(name);
                };

                // Inisialisasi awal: tampilkan SEMUA data se-Sulawesi Barat
                window.resetKeSemuaWilayah();

                // ─── 2. Inisialisasi Grafik Chart.js (Tren Harga Komoditas Harian) ───
                const chartData = @json($grafikTren);
                const ctxHarga = document.getElementById('chartHarga') || document.getElementById('chartIsu');

                if (ctxHarga && chartData) {
                    const palette = [
                        '#10b981', '#ef4444', '#8b5cf6', '#f97316', '#06b6d4',
                        '#ec4899', '#3b82f6', '#14b8a6', '#f59e0b', '#6366f1',
                        '#84cc16', '#a855f7', '#0284c7', '#d97706', '#dc2626'
                    ];

                    const topKomoditas = chartData.top_komoditas || [];
                    const allKomoditas = chartData.all_komoditas || [];

                    // Fungsi pembentuk datasets (tanpa rata2 harga pasar, murni harga komoditas harian)
                    function buildDatasets(filterMode) {
                        const datasets = [];

                        if (filterMode === 'disparitas') {
                            // Mode Default: Komoditas yang tampil di Disparitas Harga Terbesar vs HET
                            topKomoditas.forEach((topK, idx) => {
                                const color = palette[idx % palette.length];
                                if (topK.chart_prices && topK.chart_prices.length > 0) {
                                    datasets.push({
                                        label: `${topK.name} (Rp/${topK.satuan || 'kg'})`,
                                        data: topK.chart_prices,
                                        borderColor: color,
                                        backgroundColor: 'transparent',
                                        borderWidth: 2.2,
                                        pointRadius: 3.5,
                                        pointHoverRadius: 6,
                                        tension: 0.35,
                                        hidden: false,
                                    });
                                }
                            });
                        } else if (filterMode === 'all') {
                            // Mode Semua Komoditas Terdata
                            allKomoditas.forEach((kom, idx) => {
                                const color = palette[idx % palette.length];
                                if (kom.chart_prices && kom.chart_prices.length > 0) {
                                    datasets.push({
                                        label: `${kom.name} (Rp/${kom.satuan || 'kg'})`,
                                        data: kom.chart_prices,
                                        borderColor: color,
                                        backgroundColor: 'transparent',
                                        borderWidth: 1.8,
                                        pointRadius: 2.5,
                                        pointHoverRadius: 5,
                                        tension: 0.35,
                                        hidden: false,
                                    });
                                }
                            });
                        } else {
                            // Mode Komoditas Spesifik
                            const selectedId = parseInt(filterMode, 10);
                            const selectedKom = allKomoditas.find(k => k.id === selectedId) || topKomoditas.find(k => k.id === selectedId);

                            if (selectedKom) {
                                datasets.push({
                                    label: `Harga Aktual: ${selectedKom.name} (Rp/${selectedKom.satuan || 'kg'})`,
                                    data: selectedKom.chart_prices,
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.10)',
                                    fill: true,
                                    borderWidth: 2.5,
                                    pointBackgroundColor: '#10b981',
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 1.5,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    tension: 0.35,
                                });

                                if (selectedKom.het) {
                                    datasets.push({
                                        label: `Batas Acuan HET (Rp ${selectedKom.het.toLocaleString('id-ID')})`,
                                        data: Array(chartData.labels.length).fill(selectedKom.het),
                                        borderColor: '#ef4444',
                                        backgroundColor: 'transparent',
                                        borderDash: [5, 5],
                                        borderWidth: 2,
                                        pointRadius: 2,
                                        pointHoverRadius: 4,
                                        tension: 0,
                                    });
                                }
                            }
                        }

                        return datasets;
                    }

                    const chartInstance = new Chart(ctxHarga, {
                        type: 'line',
                        data: {
                            labels: chartData.labels,
                            datasets: buildDatasets('disparitas')
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: true,
                                    mode: 'index',
                                    intersect: false,
                                    backgroundColor: 'rgba(15, 23, 42, 0.92)',
                                    titleColor: '#ffffff',
                                    titleFont: {
                                        family: 'Poppins',
                                        weight: '600',
                                        size: 12
                                    },
                                    bodyColor: '#e2e8f0',
                                    bodyFont: {
                                        family: 'Poppins',
                                        size: 12
                                    },
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1,
                                    padding: 10,
                                    boxPadding: 5,
                                    usePointStyle: true,
                                    callbacks: {
                                        title: function(items) {
                                            if (!items.length) return '';
                                            return 'Tanggal: ' + items[0].label;
                                        },
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null && context.parsed.y !== undefined) {
                                                label += 'Rp ' + Number(context.parsed.y).toLocaleString('id-ID');
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.05)'
                                    },
                                    ticks: {
                                        color: '#94a3b8',
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: false,
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.05)'
                                    },
                                    ticks: {
                                        color: '#94a3b8',
                                        font: {
                                            size: 11
                                        },
                                        callback: function(value) {
                                            return 'Rp ' + (value >= 1000 ? Math.round(value / 1000) + 'k' : value);
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Event listener untuk Filter Komoditas (Tombol & Dropdown)
                    const filterSelect = document.getElementById('filterKomoditasChart');
                    const filterBadge = document.getElementById('chartFilterBadge');
                    const filterBadgeText = document.getElementById('chartFilterBadgeText');
                    const btnDisparitas = document.getElementById('btnFilterDisparitas');
                    const btnAll = document.getElementById('btnFilterAll');

                    function syncFilterButtons(mode) {
                        if (btnDisparitas && btnAll) {
                            if (mode === 'disparitas') {
                                btnDisparitas.className = 'sp-btn sp-btn-sm sp-btn-primary';
                                btnAll.className = 'sp-btn sp-btn-sm sp-btn-secondary';
                            } else if (mode === 'all') {
                                btnDisparitas.className = 'sp-btn sp-btn-sm sp-btn-secondary';
                                btnAll.className = 'sp-btn sp-btn-sm sp-btn-primary';
                            } else {
                                btnDisparitas.className = 'sp-btn sp-btn-sm sp-btn-secondary';
                                btnAll.className = 'sp-btn sp-btn-sm sp-btn-secondary';
                            }
                        }
                    }

                    function applyChartFilter(mode) {
                        chartInstance.data.datasets = buildDatasets(mode);
                        chartInstance.update();
                        syncFilterButtons(mode);

                        if (filterBadgeText) {
                            if (mode === 'disparitas') {
                                filterBadgeText.textContent = 'Disparitas & Isu';
                                if (filterBadge) filterBadge.className = 'sp-badge badge-baru';
                            } else if (mode === 'all') {
                                filterBadgeText.textContent = allKomoditas.length + ' Komoditas';
                                if (filterBadge) filterBadge.className = 'sp-badge badge-intervensi';
                            } else {
                                const opt = filterSelect ? filterSelect.options[filterSelect.selectedIndex] : null;
                                filterBadgeText.textContent = opt ? opt.text.split('(')[0].trim() : 'Komoditas';
                                if (filterBadge) filterBadge.className = 'sp-badge badge-selesai';
                            }
                        }
                    }

                    if (btnDisparitas) {
                        btnDisparitas.addEventListener('click', () => {
                            if (filterSelect) filterSelect.value = 'disparitas';
                            applyChartFilter('disparitas');
                        });
                    }

                    if (btnAll) {
                        btnAll.addEventListener('click', () => {
                            if (filterSelect) filterSelect.value = 'all';
                            applyChartFilter('all');
                        });
                    }

                    if (filterSelect) {
                        filterSelect.addEventListener('change', (e) => {
                            applyChartFilter(e.target.value);
                        });
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
