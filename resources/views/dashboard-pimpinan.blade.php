<x-app-layout>
    <x-slot name="title">Ringkasan Eksekutif Pimpinan</x-slot>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
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
        }

        .kab-card-item:hover {
            border-color: var(--clr-accent-3);
            transform: translateY(-2px);
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

    {{-- Executive Header & Filter Waktu --}}
    <div class="pimpinan-header-block">
        <div>
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.4rem;">
                <span class="sp-badge badge-intervensi" style="font-size:0.75rem;letter-spacing:0.04em;text-transform:uppercase;">
                    Monitoring Eksekutif
                </span>
                <span style="font-size:0.85rem;color:var(--clr-ink-3);">
                    Pemerintah Provinsi Sulawesi Barat
                </span>
            </div>
            <h1 style="font-size:1.75rem;font-weight:700;margin:0 0 0.35rem;letter-spacing:-0.03em;color:var(--clr-ink);">
                Ringkasan Eksekutif Perkembangan Isu & Harga Pasar
            </h1>
            <p style="color:var(--clr-ink-2);font-size:0.92rem;margin:0;">
                Pantauan terintegrasi stabilitas komoditas bahan pokok dan sebaran titik penanganan di 6 Kabupaten Sulawesi Barat.
            </p>
        </div>

        {{-- Filter Periode (Hari, Minggu, Bulan, Tahun) --}}
        <div>
            <div style="font-size:0.78rem;color:var(--clr-ink-3);margin-bottom:6px;font-weight:600;text-align:right;">
                PERIODE DATA: <span style="color:var(--clr-accent-3);">{{ $periodeLabel }}</span>
            </div>
            <div class="filter-periode-pills" role="tablist" aria-label="Filter Periode Analisis">
                <a href="{{ route('dashboard', ['periode' => 'hari']) }}"
                   class="periode-pill-btn {{ $periode === 'hari' ? 'active' : '' }}">
                   <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                   Hari Ini
                </a>
                <a href="{{ route('dashboard', ['periode' => 'minggu']) }}"
                   class="periode-pill-btn {{ $periode === 'minggu' ? 'active' : '' }}">
                   <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                   7 Hari Terakhir
                </a>
                <a href="{{ route('dashboard', ['periode' => 'bulan']) }}"
                   class="periode-pill-btn {{ $periode === 'bulan' ? 'active' : '' }}">
                   <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                   Bulan Ini
                </a>
                <a href="{{ route('dashboard', ['periode' => 'tahun']) }}"
                   class="periode-pill-btn {{ $periode === 'tahun' ? 'active' : '' }}">
                   <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                   Tahun Ini
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Info Jika Ada --}}
    @if(session('info'))
    <div style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3);color:#93c5fd;padding:12px 18px;border-radius:8px;margin-bottom:1.5rem;font-size:0.9rem;">
        {{ session('info') }}
    </div>
    @endif

    {{-- Ringkasan Eksekutif Stabilitas Pangan (Executive Briefing) --}}
    <div class="sp-card sp-executive-briefing" style="margin-bottom:1.75rem;border-left:4px solid {{ $statusDaerahLevel === 'kritis' ? 'var(--clr-kritis)' : ($statusDaerahLevel === 'waspada' ? 'var(--clr-intervensi)' : 'var(--clr-selesai)') }};">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1.25rem;">
            <div style="display:flex;align-items:center;gap:1rem;flex:1;min-width:300px;">
                <div style="width:48px;height:48px;border-radius:12px;background:{{ $statusDaerahLevel === 'kritis' ? 'rgba(239,68,68,0.15)' : ($statusDaerahLevel === 'waspada' ? 'rgba(245,158,11,0.15)' : 'rgba(16,185,129,0.15)') }};display:flex;align-items:center;justify-content:center;color:{{ $statusDaerahLevel === 'kritis' ? '#ef4444' : ($statusDaerahLevel === 'waspada' ? '#f59e0b' : '#10b981') }};flex-shrink:0;">
                    @if($statusDaerahLevel === 'kritis')
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    @elseif($statusDaerahLevel === 'waspada')
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    @else
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    @endif
                </div>
                <div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <span style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;color:var(--clr-ink-3);font-weight:700;">Status Stabilitas Pangan Daerah</span>
                        <span class="sp-badge {{ $statusDaerahLevel === 'kritis' ? 'badge-kritis' : ($statusDaerahLevel === 'waspada' ? 'badge-intervensi' : 'badge-selesai') }}" style="font-size:0.72rem;">
                            {{ $statusDaerahLabel }}
                        </span>
                    </div>
                    <div style="font-size:0.95rem;color:var(--clr-ink);font-weight:600;line-height:1.45;">
                        {{ $ringkasanEksekutif }}
                    </div>
                </div>
            </div>

            {{-- Indikator Stabilitas Komoditas --}}
            <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
                <div class="kpi-mini-box kpi-stabil" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);padding:8px 14px;border-radius:8px;text-align:center;min-width:105px;">
                    <div class="kpi-mini-val" style="font-size:1.3rem;font-weight:700;color:#34d399;">{{ $komoditasStabilCount }}</div>
                    <div class="kpi-mini-lbl" style="font-size:0.7rem;color:#a7f3d0;text-transform:uppercase;font-weight:700;">Stabil (HET)</div>
                </div>
                <div class="kpi-mini-box kpi-waspada" style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.25);padding:8px 14px;border-radius:8px;text-align:center;min-width:105px;">
                    <div class="kpi-mini-val" style="font-size:1.3rem;font-weight:700;color:#fbbf24;">{{ $komoditasWaspadaCount }}</div>
                    <div class="kpi-mini-lbl" style="font-size:0.7rem;color:#fde68a;text-transform:uppercase;font-weight:700;">Waspada (5-15%)</div>
                </div>
                <div class="kpi-mini-box kpi-kritis" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);padding:8px 14px;border-radius:8px;text-align:center;min-width:105px;">
                    <div class="kpi-mini-val" style="font-size:1.3rem;font-weight:700;color:#f87171;">{{ $komoditasKritisCount }}</div>
                    <div class="kpi-mini-lbl" style="font-size:0.7rem;color:#fecaca;text-transform:uppercase;font-weight:700;">Melambung (&gt;15%)</div>
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

        {{-- Rata-rata SLA --}}
        <div class="sp-stat-card" style="border-left:3px solid var(--clr-verifikasi);">
            <span class="sp-stat-label">Rata-rata Durasi Resolusi (SLA)</span>
            <div class="sp-stat-value" style="color:var(--clr-verifikasi);">{{ round($avgSla, 1) }} <span style="font-size:1rem;font-weight:500;">Hari</span></div>
            <span class="sp-stat-sub">Kecepatan intervensi dinas</span>
        </div>
    </div>

    {{-- BAGIAN 1: PETA SEBARAN TITIK ISU SULAWESI BARAT --}}
    <div class="sp-card" style="margin-bottom:2rem;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.25rem;">
                    <span class="sp-badge badge-lapangan">Spasial Wilayah</span>
                    <h2 style="font-size:1.2rem;font-weight:700;margin:0;letter-spacing:-0.02em;">
                        Peta Sebaran Titik Isu & Status Kerawanan 6 Kabupaten
                    </h2>
                </div>
                <p style="color:var(--clr-ink-3);font-size:0.85rem;margin:0;">
                    Monitoring spasial wilayah Sulawesi Barat: titik merah menandakan adanya isu kritis, kuning menandakan proses intervensi aktif, dan hijau berstatus kondusif.
                </p>
            </div>

            <div style="display:flex;align-items:center;gap:12px;font-size:0.75rem;">
                <span style="display:inline-flex;align-items:center;gap:5px;color:#f87171;">
                    <span style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block;"></span> Kritis
                </span>
                <span style="display:inline-flex;align-items:center;gap:5px;color:#fbbf24;">
                    <span style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block;"></span> Waspada
                </span>
                <span style="display:inline-flex;align-items:center;gap:5px;color:#34d399;">
                    <span style="width:10px;height:10px;border-radius:50%;background:#10b981;display:inline-block;"></span> Kondusif
                </span>
            </div>
        </div>

        {{-- Map Container --}}
        <div id="petaSulbar" class="peta-sulbar-wrap" role="region" aria-label="Peta Sulawesi Barat"></div>

        {{-- List Kabupaten Cards --}}
        <div class="kab-grid">
            @foreach($petaKabupaten as $kab)
            <div class="kab-card-item" onclick="focusKabupaten({{ $kab['lat'] }}, {{ $kab['lng'] }}, '{{ $kab['name'] }}')">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <strong style="color:var(--clr-ink);font-size:0.95rem;">{{ $kab['name'] }}</strong>
                    <span class="map-kab-badge badge-{{ $kab['status_level'] }}">
                        {{ strtoupper($kab['status_level']) }}
                    </span>
                </div>
                <div style="font-size:0.8rem;color:var(--clr-ink-2);margin-bottom:4px;">
                    Isu Terdata: <strong style="color:var(--clr-ink);">{{ $kab['total_isu_periode'] }}</strong> (Aktif: {{ $kab['isu_aktif'] }})
                </div>
                <div style="font-size:0.75rem;color:var(--clr-ink-3);">
                    Komoditas Paling Disorot: <span style="color:var(--clr-accent-3);font-weight:600;">{{ $kab['komoditas_utama'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- BAGIAN 2: GRAFIK TREN PERIODE & ANALITIK WAKTU --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(360px, 1fr));gap:1.5rem;margin-bottom:2rem;">
        {{-- Grafik Isu Masuk vs Selesai --}}
        <div class="sp-card">
            <div style="margin-bottom:1.25rem;">
                <h3 style="font-size:1.1rem;font-weight:700;margin:0 0 0.25rem;color:var(--clr-ink);">
                    Tren Dinamika Isu ({{ $periodeLabel }})
                </h3>
                <p style="color:var(--clr-ink-3);font-size:0.8rem;margin:0;">
                    Perbandingan volume isu masuk versus kecepatan penyelesaian isu oleh tim.
                </p>
            </div>
            <div class="chart-box">
                <canvas id="chartIsu"></canvas>
            </div>
        </div>

        {{-- Grafik Komoditas Paling Berfluktuasi --}}
        <div class="sp-card">
            <div style="margin-bottom:1.25rem;">
                <h3 style="font-size:1.1rem;font-weight:700;margin:0 0 0.25rem;color:var(--clr-ink);">
                    Disparitas Harga Terbesar vs HET
                </h3>
                <p style="color:var(--clr-ink-3);font-size:0.8rem;margin:0;">
                    3 komoditas dengan deviasi kenaikan tertinggi di atas patokan pemerintah.
                </p>
            </div>
            <div style="display:flex;flex-direction:column;gap:1.25rem;padding-top:0.5rem;">
                @forelse($grafikTren['top_komoditas'] as $topK)
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.88rem;margin-bottom:4px;">
                        <span style="font-weight:600;color:var(--clr-ink);">{{ $topK['name'] }}</span>
                        <span class="sp-badge {{ $topK['status_class'] }}" style="font-size:0.75rem;">
                            {{ $topK['persen_selisih'] > 0 ? '+' : '' }}{{ $topK['persen_selisih'] }}%
                        </span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:0.78rem;color:var(--clr-ink-3);margin-bottom:6px;">
                        <span>HET: Rp {{ number_format($topK['het'], 0, ',', '.') }}/{{ $topK['satuan'] }}</span>
                        <span style="color:var(--clr-ink-2);font-weight:600;">Aktual: Rp {{ number_format($topK['harga_aktual'], 0, ',', '.') }}</span>
                    </div>
                    <div class="price-progress-bar">
                        @php
                            $fillPct = min(100, max(10, 100 + $topK['persen_selisih']));
                            $barColor = $topK['persen_selisih'] > 15 ? '#ef4444' : ($topK['persen_selisih'] > 5 ? '#f59e0b' : '#10b981');
                        @endphp
                        <div class="price-progress-fill" style="width: {{ $fillPct }}%; background: {{ $barColor }};"></div>
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
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.25rem;">
                    <span class="sp-badge badge-selesai">Stabilisasi Harga</span>
                    <h2 style="font-size:1.2rem;font-weight:700;margin:0;letter-spacing:-0.02em;">
                        Perkembangan & Deviasi Harga Komoditas Pokok vs HET
                    </h2>
                </div>
                <p style="color:var(--clr-ink-3);font-size:0.85rem;margin:0;">
                    Kompilasi perbandingan harga acuan pemerintah (HET) dan harga aktual temuan lapangan per komoditas pangan di Sulawesi Barat.
                </p>
            </div>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <div style="font-size:0.82rem;color:var(--clr-ink-3);">
                    Total Komoditas: <strong style="color:var(--clr-ink);">{{ count($perkembanganHarga) }} Komoditas Terpantau</strong>
                </div>
                <a href="{{ route('prices.index') }}" class="sp-btn sp-btn-secondary sp-btn-sm" style="font-size:0.78rem;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
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
                            <span style="font-size:0.68rem;color:var(--clr-ink-3);display:block;font-weight:normal;margin-top:2px;">
                                @if(($item['sumber_harga'] ?? '') === 'Import Pasar')
                                    <span style="color:#34d399;">&bull; Data Pasar</span>
                                    @if(($item['variants_count'] ?? 0) > 1)
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
                            @if($item['selisih'] > 0)
                                <span style="color:#f87171;">+Rp {{ number_format($item['selisih'], 0, ',', '.') }} (+{{ $item['persen_selisih'] }}%)</span>
                            @elseif($item['selisih'] < 0)
                                <span style="color:#34d399;">-Rp {{ number_format(abs($item['selisih']), 0, ',', '.') }} ({{ $item['persen_selisih'] }}%)</span>
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
                            @if($item['isu_count'] > 0)
                                <span style="font-weight:700;color:var(--clr-kritis);font-size:0.85rem;">{{ $item['isu_count'] }} isu</span>
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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            // Data titik kabupaten dari server
            const petaKabupaten = @json($petaKabupaten);
            const titikLapangan = @json($titikLapangan);

            // Simpan marker dalam objek untuk interaksi klik kartu
            window.kabMarkers = {};

            // Render Titik Kabupaten
            petaKabupaten.forEach(function (kab) {
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
                        <div style="font-size: 12px; margin-bottom: 4px; color: #cbd5e1;">Isu Aktif: <b>${kab.isu_aktif}</b></div>
                        <div style="font-size: 11px; color: #94a3b8;">Komoditas Terbanyak: <b>${kab.komoditas_utama}</b></div>
                    </div>
                `;
                circleMarker.bindPopup(popupHtml);
                window.kabMarkers[kab.name] = { marker: circleMarker, lat: kab.lat, lng: kab.lng };
            });

            // Render Titik Lapangan Cek Fisik (Jika Ada)
            titikLapangan.forEach(function (tp) {
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

            // Fungsi Zoom ke Kabupaten dari Kartu
            window.focusKabupaten = function (lat, lng, name) {
                map.flyTo([lat, lng], 11, { duration: 1.2 });
                if (window.kabMarkers[name]) {
                    window.kabMarkers[name].marker.openPopup();
                }
            };

            // ─── 2. Inisialisasi Grafik Chart.js ───
            const chartData = @json($grafikTren);
            const ctxIsu = document.getElementById('chartIsu');

            if (ctxIsu && chartData) {
                new Chart(ctxIsu, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [
                            {
                                label: 'Isu Masuk',
                                data: chartData.isu_masuk,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.35,
                                fill: true,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#3b82f6',
                                pointRadius: 4
                            },
                            {
                                label: 'Isu Dituntaskan',
                                data: chartData.isu_selesai,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.35,
                                fill: true,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#10b981',
                                pointRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: '#cbd5e1', font: { size: 12 } }
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#94a3b8', font: { size: 11 } }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#94a3b8', font: { size: 11 }, precision: 0 }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
