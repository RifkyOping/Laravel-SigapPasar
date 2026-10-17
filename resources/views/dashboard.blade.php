<x-app-layout>
    <x-slot name="title">Dashboard Analitik</x-slot>

    {{-- Role Banner & Welcome --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 style="font-size:1.6rem;font-weight:700;margin:0 0 0.35rem;letter-spacing:-0.03em;">
                Selamat Datang, {{ $user->name }}
            </h1>
            <p style="color:var(--clr-ink-2);font-size:0.9rem;margin:0;">
                {{ $user->instansi ?? 'Pemerintah Provinsi' }} &bull; Peran: <span style="color:var(--clr-accent);font-weight:600;">{{ $user->role_label }}</span>
            </p>
        </div>

        <div style="display:flex;gap:0.75rem;align-items:center;">
            @if(auth()->user()->isPengawasSiber() || auth()->user()->isRole(['admin','super_admin']))
            <a href="{{ route('issues.create') }}" class="sp-btn sp-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Input Isu Baru
            </a>
            @endif

            @if(auth()->user()->isRole(['verifikator','admin','super_admin']))
            <a href="{{ route('verifikasi.index') }}" class="sp-btn sp-btn-secondary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                Verifikasi ({{ $stats['isuBaru'] }})
            </a>
            @endif
        </div>
    </div>

    {{-- Penugasan Aktif (Khusus Petugas Lapangan) --}}
    @if($user->isPetugasLapangan() && $stats['penugasanAktif']->count() > 0)
    <div class="sp-card sp-task-card" style="margin-bottom:2rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div style="display:flex;align-items:center;gap:0.6rem;">
                <span class="sp-badge badge-ditugaskan">Tugas Aktif</span>
                <span style="font-weight:600;font-size:1.05rem;">Penugasan Lapangan Mendesak</span>
            </div>
            <a href="{{ route('laporan.index') }}" class="sp-btn sp-btn-ghost sp-btn-sm">Lihat Semua</a>
        </div>
        <div class="sp-table-wrap">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Kode Isu</th>
                        <th>Komoditas & Lokasi</th>
                        <th>Tanggal Penugasan</th>
                        <th>Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['penugasanAktif'] as $asgn)
                    <tr>
                        <td class="cell-code">{{ $asgn->issue->kode_isu }}</td>
                        <td>
                            <strong style="color:var(--clr-ink);">{{ $asgn->issue->komoditas->name ?? '-' }}</strong><br>
                            <span style="font-size:0.75rem;color:var(--clr-ink-3);">{{ $asgn->issue->kabupaten->name ?? '-' }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($asgn->tanggal_penugasan)->isoFormat('D MMM YYYY') }}</td>
                        <td>
                            <a href="{{ route('laporan.show', $asgn) }}" class="sp-btn sp-btn-primary sp-btn-sm">
                                Check-in / Lapor
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- KPI Cards Row --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:1rem;margin-bottom:2rem;">
        <div class="sp-stat-card">
            <span class="sp-stat-label">Total Isu Terpantau</span>
            <div class="sp-stat-value">{{ $stats['totalIsu'] }}</div>
            <span class="sp-stat-sub">Semua platform media</span>
        </div>

        <div class="sp-stat-card" style="border-left:3px solid var(--clr-baru);">
            <span class="sp-stat-label">Menunggu Verifikasi</span>
            <div class="sp-stat-value" style="color:var(--clr-baru);">{{ $stats['isuBaru'] }}</div>
            <span class="sp-stat-sub">Laporan siber baru</span>
        </div>

        <div class="sp-stat-card" style="border-left:3px solid var(--clr-kritis);">
            <span class="sp-stat-label">Prioritas Kritis</span>
            <div class="sp-stat-value" style="color:var(--clr-kritis);">{{ $stats['isuKritis'] }}</div>
            <span class="sp-stat-sub">Butuh tindakan segera</span>
        </div>

        <div class="sp-stat-card" style="border-left:3px solid var(--clr-lapangan);">
            <span class="sp-stat-label">Sedang di Lapangan</span>
            <div class="sp-stat-value" style="color:var(--clr-lapangan);">{{ $stats['isuDilapangan'] }}</div>
            <span class="sp-stat-sub">Cek fisik & verifikasi harga</span>
        </div>

        <div class="sp-stat-card" style="border-left:3px solid var(--clr-intervensi);">
            <span class="sp-stat-label">Dalam Intervensi</span>
            <div class="sp-stat-value" style="color:var(--clr-intervensi);">{{ $stats['isuIntervensi'] }}</div>
            <span class="sp-stat-sub">Operasi pasar / Bulog</span>
        </div>

        <div class="sp-stat-card" style="border-left:3px solid var(--clr-selesai);">
            <span class="sp-stat-label">Selesai / Ditutup</span>
            <div class="sp-stat-value" style="color:var(--clr-selesai);">{{ $stats['isuSelesai'] }}</div>
            <span class="sp-stat-sub">SLA Rata-rata: {{ round($stats['avgSla'] ?? 0, 1) }} hari</span>
        </div>
    </div>

    {{-- Analytics Grid: Top Komoditas & Top Wilayah --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:1.5rem;margin-bottom:2rem;">
        {{-- Komoditas Terbanyak Dikeluhkan --}}
        <div class="sp-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
                <h2 style="font-size:1.05rem;font-weight:600;margin:0;">Distribusi Komoditas Pangan</h2>
                <span style="font-size:0.75rem;color:var(--clr-ink-3);">5 Teratas</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:1rem;">
                @forelse($stats['perKomoditas'] as $item)
                @php
                    $pct = $stats['totalIsu'] > 0 ? round(($item->total / $stats['totalIsu']) * 100) : 0;
                @endphp
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:0.35rem;">
                        <span style="font-weight:500;color:var(--clr-ink);">{{ $item->komoditas->name ?? 'Lainnya' }}</span>
                        <span style="color:var(--clr-accent);font-weight:600;">{{ $item->total }} laporan ({{ $pct }}%)</span>
                    </div>
                    <div style="height:6px;background:var(--clr-ground-2);border-radius:3px;overflow:hidden;">
                        <div style="height:100%;width:{{ max($pct, 4) }}%;background:var(--clr-accent);border-radius:3px;"></div>
                    </div>
                </div>
                @empty
                <div style="color:var(--clr-ink-3);font-size:0.85rem;text-align:center;padding:1.5rem 0;">
                    Belum ada data komoditas tercatat.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Wilayah / Kabupaten Paling Terdampak --}}
        <div class="sp-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
                <h2 style="font-size:1.05rem;font-weight:600;margin:0;">Wilayah Pantauan Utama</h2>
                <span style="font-size:0.75rem;color:var(--clr-ink-3);">5 Teratas</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:1rem;">
                @forelse($stats['perKabupaten'] as $item)
                @php
                    $pct = $stats['totalIsu'] > 0 ? round(($item->total / $stats['totalIsu']) * 100) : 0;
                @endphp
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:0.35rem;">
                        <span style="font-weight:500;color:var(--clr-ink);">{{ $item->kabupaten->name ?? 'Lainnya' }}</span>
                        <span style="color:var(--clr-lapangan);font-weight:600;">{{ $item->total }} isu</span>
                    </div>
                    <div style="height:6px;background:var(--clr-ground-2);border-radius:3px;overflow:hidden;">
                        <div style="height:100%;width:{{ max($pct, 4) }}%;background:var(--clr-lapangan);border-radius:3px;"></div>
                    </div>
                </div>
                @empty
                <div style="color:var(--clr-ink-3);font-size:0.85rem;text-align:center;padding:1.5rem 0;">
                    Belum ada data wilayah tercatat.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Isu Terbaru Table --}}
    <div class="sp-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
            <div>
                <h2 style="font-size:1.05rem;font-weight:600;margin:0;">Laporan Isu Terkini</h2>
                <p style="color:var(--clr-ink-3);font-size:0.78rem;margin:0.2rem 0 0;">Daftar pantauan siber yang baru masuk dan sedang diproses</p>
            </div>
            <a href="{{ route('issues.index') }}" class="sp-btn sp-btn-ghost sp-btn-sm">
                Lihat Semua Isu &rarr;
            </a>
        </div>

        <div class="sp-table-wrap">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Kode Isu</th>
                        <th>Komoditas</th>
                        <th>Kabupaten / Pasar</th>
                        <th>Virality Score</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Pelapor</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stats['isuTerbaru'] as $isu)
                    <tr>
                        <td class="cell-code">
                            <a href="{{ route('issues.show', $isu) }}" style="color:var(--clr-accent);">
                                {{ $isu->kode_isu }}
                            </a>
                        </td>
                        <td class="cell-primary">{{ $isu->komoditas->name ?? '-' }}</td>
                        <td>
                            <div>{{ $isu->kabupaten->name ?? '-' }}</div>
                            @php $detailLokasi = $isu->lokasi_spesifik ?: ($isu->pasar->name ?? null); @endphp
                            @if($detailLokasi)
                            <small style="color:var(--clr-ink-3);">{{ $detailLokasi }}</small>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.4rem;">
                                <span style="font-weight:700;color:var(--clr-ink);">{{ number_format($isu->virality_score) }}</span>
                                <span style="font-size:0.7rem;color:var(--clr-ink-3);">pts</span>
                            </div>
                        </td>
                        <td>
                            <span class="sp-badge badge-{{ $isu->prioritas }}">
                                {{ ucfirst($isu->prioritas) }}
                            </span>
                        </td>
                        <td>
                            <span class="sp-badge badge-{{ $isu->status }}">
                                {{ $isu->status_label }}
                            </span>
                        </td>
                        <td style="font-size:0.8rem;color:var(--clr-ink-2);">
                            {{ $isu->inputBy->name ?? 'Sistem' }}
                        </td>
                        <td>
                            <a href="{{ route('issues.show', $isu) }}" class="sp-btn sp-btn-ghost sp-btn-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:2.5rem 0;color:var(--clr-ink-3);">
                            Belum ada isu pasar yang dilaporkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
