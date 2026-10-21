<x-app-layout>
    <x-slot name="title">Detail Isu {{ $issue->kode_isu }}</x-slot>

    {{-- Breadcrumb & Actions --}}
    <div
        style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <a href="{{ route('issues.index') }}" class="sp-btn sp-btn-ghost sp-btn-sm" style="margin-bottom:0.6rem;">
                &larr; Kembali ke Daftar Isu
            </a>
            <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
                <h1 style="font-size:1.6rem;font-weight:700;margin:0;letter-spacing:-0.03em;">
                    {{ $issue->kode_isu }}
                </h1>
                <span class="sp-badge badge-{{ $issue->status }}" style="font-size:0.8rem;padding:0.3rem 0.75rem;">
                    {{ $issue->status_label }}
                </span>
                <span class="sp-badge badge-{{ $issue->prioritas }}" style="font-size:0.8rem;padding:0.3rem 0.75rem;">
                    Prioritas {{ ucfirst($issue->prioritas) }}
                </span>
            </div>
            <p style="color:var(--clr-ink-2);font-size:0.85rem;margin:0.25rem 0 0;">
                Terdeteksi pada {{ $issue->tanggal_temuan->isoFormat('D MMMM YYYY, HH:mm') }} &bull; Diinput oleh:
                <strong>{{ $issue->inputBy->name ?? 'Sistem' }}</strong>
            </p>
        </div>

        {{-- Contextual Action Buttons --}}
        <div style="display:flex;gap:0.6rem;align-items:center;flex-wrap:wrap;">
            {{-- Edit jika baru (hanya untuk pengawas pembuat atau admin, bukan pimpinan) --}}
            @if (
                !auth()->user()->isPimpinan() &&
                    (($issue->status === 'baru' && auth()->id() === $issue->input_by) ||
                        auth()->user()->isRole(['admin', 'super_admin'])))
                <a href="{{ route('issues.edit', $issue) }}" class="sp-btn sp-btn-secondary sp-btn-sm">
                    Edit Data
                </a>
            @endif

            {{-- Tahap 2: Penugasan Admin --}}
            @if (in_array($issue->status, ['baru', 'diverifikasi']) &&
                    auth()->user()->isRole(['admin', 'super_admin']))
                <a href="{{ route('penugasan.index') }}" class="sp-btn sp-btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                    </svg>
                    Tugaskan Petugas
                </a>
            @endif

            {{-- Tahap 4: Intervensi --}}
            @if (in_array($issue->status, ['diverifikasi', 'ditugaskan', 'di_lapangan']) &&
                    auth()->user()->isRole(['admin', 'super_admin']))
                <a href="{{ route('intervensi.create', $issue) }}" class="sp-btn sp-btn-secondary">
                    + Tambah Intervensi
                </a>
            @endif

            {{-- Tahap 5: Resolusi/Tutup Isu --}}
            @if (
                !in_array($issue->status, ['selesai', 'hoaks', 'ditutup']) &&
                    auth()->user()->isRole(['admin', 'super_admin']))
                <a href="{{ route('resolusi.create', $issue) }}" class="sp-btn sp-btn-primary">
                    Tutup / Selesaikan Isu &rarr;
                </a>
            @endif

            {{-- Badge Khusus Pimpinan (Hanya Baca Progres) --}}
            @if (auth()->user()->isPimpinan())
                <span class="sp-badge badge-selesai"
                    style="font-size:0.8rem;padding:0.4rem 0.85rem;display:inline-flex;align-items:center;gap:0.4rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    Mode Pantauan Eksekutif: Hanya Baca
                </span>
            @endif
        </div>
    </div>

    {{-- Visual 5-Stage Progress Tracker --}}
    @php
        $stage1Done = true;
        $stage2Done =
            in_array($issue->status, ['ditugaskan', 'di_lapangan', 'intervensi', 'selesai', 'ditutup']) ||
            $issue->assignments->count() > 0;
        $stage3Done =
            in_array($issue->status, ['di_lapangan', 'intervensi', 'selesai', 'ditutup']) ||
            $issue->assignments->where('status', 'selesai')->count() > 0;
        $stage4Done =
            in_array($issue->status, ['intervensi', 'selesai', 'ditutup']) || $issue->interventions->count() > 0;
        $stage5Done = in_array($issue->status, ['selesai', 'ditutup']) || $issue->resolution !== null;

        $activeStage = 1;
        if ($stage5Done) {
            $activeStage = 5;
        } elseif ($stage4Done) {
            $activeStage = 4;
        } elseif ($stage3Done) {
            $activeStage = 3;
        } elseif ($stage2Done) {
            $activeStage = 2;
        }
    @endphp

    <div class="sp-card" style="margin-bottom:1.5rem;padding:1.25rem 1.5rem;">
        <div
            style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem;">
            <div
                style="font-size:0.9rem;font-weight:700;color:var(--clr-ink);display:flex;align-items:center;gap:0.5rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                </svg>
                Progres Penanganan Isu (5 Tahap Rantai Komando)
            </div>
            <span style="font-size:0.8rem;color:var(--clr-ink-3);">
                Tahap Saat Ini: <strong style="color:var(--clr-accent);">Tahap {{ $activeStage }}</strong> &bull;
                Status: <strong style="color:var(--clr-ink);">{{ $issue->status_label }}</strong>
            </span>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(130px, 1fr));gap:0.6rem;">
            {{-- Tahap 1 --}}
            <div
                style="background:var(--clr-ground-4);border-radius:6px;padding:0.75rem;border-top:3px solid {{ $stage1Done ? 'var(--clr-selesai)' : 'var(--clr-border)' }};">
                <div
                    style="font-size:0.7rem;color:{{ $stage1Done ? 'var(--clr-selesai)' : 'var(--clr-ink-3)' }};font-weight:700;text-transform:uppercase;">
                    Tahap 1</div>
                <div style="font-size:0.85rem;font-weight:600;color:var(--clr-ink);margin-top:0.2rem;">Deteksi Siber
                </div>
                <div style="font-size:0.75rem;color:var(--clr-ink-3);margin-top:0.2rem;">
                    {{ $issue->tanggal_temuan->format('d/m/Y') }}</div>
            </div>

            {{-- Tahap 2 --}}
            <div
                style="background:var(--clr-ground-4);border-radius:6px;padding:0.75rem;border-top:3px solid {{ $stage2Done ? 'var(--clr-selesai)' : ($activeStage == 2 ? 'var(--clr-accent)' : 'var(--clr-border)') }};">
                <div
                    style="font-size:0.7rem;color:{{ $stage2Done ? 'var(--clr-selesai)' : ($activeStage == 2 ? 'var(--clr-accent)' : 'var(--clr-ink-3)') }};font-weight:700;text-transform:uppercase;">
                    Tahap 2</div>
                <div style="font-size:0.85rem;font-weight:600;color:var(--clr-ink);margin-top:0.2rem;">Penugasan</div>
                <div style="font-size:0.75rem;color:var(--clr-ink-3);margin-top:0.2rem;">
                    {{ $issue->assignments->first() ? \Carbon\Carbon::parse($issue->assignments->first()->tanggal_penugasan)->format('d/m/Y') : 'Menunggu' }}
                </div>
            </div>

            {{-- Tahap 3 --}}
            <div
                style="background:var(--clr-ground-4);border-radius:6px;padding:0.75rem;border-top:3px solid {{ $stage3Done ? 'var(--clr-selesai)' : ($activeStage == 3 ? 'var(--clr-accent)' : 'var(--clr-border)') }};">
                <div
                    style="font-size:0.7rem;color:{{ $stage3Done ? 'var(--clr-selesai)' : ($activeStage == 3 ? 'var(--clr-accent)' : 'var(--clr-ink-3)') }};font-weight:700;text-transform:uppercase;">
                    Tahap 3</div>
                <div style="font-size:0.85rem;font-weight:600;color:var(--clr-ink);margin-top:0.2rem;">Cek Lapangan
                </div>
                <div style="font-size:0.75rem;color:var(--clr-ink-3);margin-top:0.2rem;">
                    {{ $issue->assignments->where('status', 'selesai')->first() ? 'Laporan Selesai' : ($issue->assignments->count() > 0 ? 'Sedang Dicek' : 'Belum') }}
                </div>
            </div>

            {{-- Tahap 4 --}}
            <div
                style="background:var(--clr-ground-4);border-radius:6px;padding:0.75rem;border-top:3px solid {{ $stage4Done ? 'var(--clr-selesai)' : ($activeStage == 4 ? 'var(--clr-accent)' : 'var(--clr-border)') }};">
                <div
                    style="font-size:0.7rem;color:{{ $stage4Done ? 'var(--clr-selesai)' : ($activeStage == 4 ? 'var(--clr-accent)' : 'var(--clr-ink-3)') }};font-weight:700;text-transform:uppercase;">
                    Tahap 4</div>
                <div style="font-size:0.85rem;font-weight:600;color:var(--clr-ink);margin-top:0.2rem;">Intervensi</div>
                <div style="font-size:0.75rem;color:var(--clr-ink-3);margin-top:0.2rem;">
                    {{ $issue->interventions->count() > 0 ? $issue->interventions->count() . ' Tindakan' : 'Belum' }}
                </div>
            </div>

            {{-- Tahap 5 --}}
            <div
                style="background:var(--clr-ground-4);border-radius:6px;padding:0.75rem;border-top:3px solid {{ $stage5Done ? 'var(--clr-selesai)' : 'var(--clr-border)' }};">
                <div
                    style="font-size:0.7rem;color:{{ $stage5Done ? 'var(--clr-selesai)' : 'var(--clr-ink-3)' }};font-weight:700;text-transform:uppercase;">
                    Tahap 5</div>
                <div style="font-size:0.85rem;font-weight:600;color:var(--clr-ink);margin-top:0.2rem;">Resolusi /
                    Selesai</div>
                <div style="font-size:0.75rem;color:var(--clr-ink-3);margin-top:0.2rem;">
                    {{ $issue->resolution ? 'Tuntas (' . $issue->resolution->durasi_penanganan_hari . ' Hari)' : 'Dalam Proses' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Main 2-Column Content --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;align-items:start;">

        {{-- Left: The 5 Stages Workflow Details --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- TAHAP 1: DETEKSI & DATA AWAL --}}
            <div class="sp-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
                    <div style="display:flex;align-items:center;gap:0.6rem;">
                        <span class="sp-badge badge-baru">Tahap 1</span>
                        <h2 style="font-size:1.1rem;font-weight:600;margin:0;">Deteksi & Data Siber</h2>
                    </div>
                    <span style="font-size:0.8rem;color:var(--clr-ink-3);">Virality Score:
                        <strong>{{ number_format($issue->virality_score) }}</strong></span>
                </div>

                <div
                    style="background:var(--clr-ground-4);border-radius:6px;padding:1.25rem;margin-bottom:1.25rem;border-left:3px solid var(--clr-accent);">
                    <div
                        style="font-size:0.75rem;color:var(--clr-ink-3);margin-bottom:0.4rem;text-transform:uppercase;letter-spacing:0.06em;">
                        Kutipan Postingan Viral:</div>
                    <blockquote
                        style="margin:0;font-size:1.05rem;line-height:1.5;color:var(--clr-ink);font-style:italic;">
                        "{{ $issue->kutipan_postingan }}"
                    </blockquote>
                </div>

                <div
                    style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1rem;margin-bottom:1.25rem;">
                    <div>
                        <div class="sp-stat-label">Platform Sumber</div>
                        <div style="font-size:0.95rem;font-weight:600;color:var(--clr-ink);">{{ $issue->platform }}
                        </div>
                        <div style="font-size:0.8rem;color:var(--clr-ink-3);">Akun:
                            {{ $issue->nama_akun ?? 'Tidak Disebutkan' }}</div>
                    </div>

                    <div>
                        <div class="sp-stat-label">Interaksi & Mention</div>
                        <div style="font-size:0.95rem;font-weight:600;color:var(--clr-ink);">
                            {{ number_format($issue->jumlah_interaksi) }} Interaksi</div>
                        <div style="font-size:0.8rem;color:var(--clr-ink-3);">
                            {{ number_format($issue->jumlah_mention) }} Mention/Komen</div>
                    </div>

                    <div>
                        <div class="sp-stat-label">Komoditas & Kategori</div>
                        <div style="font-size:0.95rem;font-weight:600;color:var(--clr-ink);">
                            {{ $issue->komoditas->name ?? '-' }}</div>
                        <div style="font-size:0.8rem;color:var(--clr-accent);">
                            {{ ucfirst(str_replace('_', ' ', $issue->kategori_isu)) }}</div>
                    </div>

                    <div>
                        <div class="sp-stat-label">Wilayah & Lokasi</div>
                        <div style="font-size:0.95rem;font-weight:600;color:var(--clr-ink);">
                            {{ $issue->kabupaten->name ?? '-' }}</div>
                        @php $detailLokasi = $issue->lokasi_spesifik ?: ($issue->pasar->name ?? null); @endphp
                        @if ($detailLokasi)
                            <div style="font-size:0.8rem;color:var(--clr-ink-3);">{{ $detailLokasi }}</div>
                        @endif
                    </div>
                </div>

                @if ($issue->link_postingan)
                    <div style="margin-bottom:1rem;font-size:0.85rem;">
                        <span style="color:var(--clr-ink-3);">Tautan Postingan:</span>
                        <a href="{{ $issue->link_postingan }}" target="_blank" rel="noopener"
                            style="margin-left:0.4rem;text-decoration:underline;">
                            {{ $issue->link_postingan }} &nearr;
                        </a>
                    </div>
                @endif

                @if ($issue->evidences->where('tahap', 'input')->count() > 0)
                    <div>
                        <div class="sp-stat-label" style="margin-bottom:0.5rem;">Bukti Tangkapan Layar:</div>
                        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                            @foreach ($issue->evidences->where('tahap', 'input') as $evi)
                                <div
                                    style="border:1px solid var(--clr-border);border-radius:6px;overflow:hidden;background:var(--clr-ground-2);max-width:240px;">
                                    @if (str_contains($evi->file_type ?? '', 'image') ||
                                            in_array(strtolower(pathinfo($evi->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                                        <a href="{{ asset('storage/' . $evi->file_path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $evi->file_path) }}" alt="Screenshot"
                                                style="width:100%;height:140px;object-fit:cover;">
                                        </a>
                                    @else
                                        <div style="padding:1rem;text-align:center;">
                                            <a href="{{ asset('storage/' . $evi->file_path) }}" target="_blank"
                                                class="sp-btn sp-btn-ghost sp-btn-sm">
                                                Unduh Dokumen ({{ $evi->file_name }})
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- TAHAP 2 & 3: PENUGASAN & CEK LAPANGAN --}}
            <div class="sp-card">
                <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1.25rem;">
                    <span class="sp-badge badge-di_lapangan">Tahap 2 & 3</span>
                    <h2 style="font-size:1.1rem;font-weight:600;margin:0;">Penugasan Lapangan & Hasil Pengecekan Riil
                    </h2>
                </div>

                @if ($issue->assignments->count() > 0)
                    @foreach ($issue->assignments as $asgn)
                        <div
                            style="background:var(--clr-ground-4);border-radius:6px;padding:1.25rem;margin-bottom:1rem;">
                            <div
                                style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;flex-wrap:wrap;gap:0.5rem;">
                                <div>
                                    <strong style="color:var(--clr-ink);font-size:0.95rem;">Petugas:
                                        {{ $asgn->petugas->name ?? '-' }}</strong>
                                    <span
                                        style="font-size:0.8rem;color:var(--clr-ink-3);">({{ $asgn->petugas->phone ?? '-' }})</span>
                                </div>
                                <span
                                    class="sp-badge {{ $asgn->status === 'selesai' ? 'badge-selesai' : 'badge-ditugaskan' }}">
                                    {{ ucfirst(str_replace('_', ' ', $asgn->status)) }}
                                </span>
                            </div>

                            <div
                                style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:0.75rem;margin-bottom:0.75rem;font-size:0.85rem;">
                                <div>
                                    <span style="color:var(--clr-ink-3);">Ditugaskan Oleh:</span>
                                    {{ $asgn->assignedBy->name ?? 'Admin' }}
                                </div>
                                <div>
                                    <span style="color:var(--clr-ink-3);">Tanggal Tugas:</span>
                                    {{ \Carbon\Carbon::parse($asgn->tanggal_penugasan)->format('d/m/Y') }}
                                </div>
                                <div>
                                    <span style="color:var(--clr-ink-3);">Metode Cek:</span>
                                    <span style="font-weight:600;color:var(--clr-ink);">
                                        {{ ($asgn->metode_verifikasi ?? 'offline') === 'online' ? 'Online (Klarifikasi Daring)' : 'Offline (Cek Fisik Lapangan)' }}
                                    </span>
                                </div>
                                <div>
                                    @if (($asgn->metode_verifikasi ?? 'offline') === 'online')
                                        <span style="color:var(--clr-ink-3);">Kontak/Medsos Sumber:</span>
                                        @php
                                            $sumberInfo = array_filter([
                                                $asgn->no_telepon_sumber,
                                                $asgn->sosmed_sumber,
                                            ]);
                                        @endphp
                                        @if (count($sumberInfo) > 0)
                                            <span style="color:var(--clr-ink);font-weight:600;">
                                                {{ implode(' • ', $sumberInfo) }}
                                            </span>
                                        @else
                                            <span style="color:var(--clr-ink-3);">Belum Divalidasi</span>
                                        @endif
                                    @else
                                        <span style="color:var(--clr-ink-3);">Koordinat GPS:</span>
                                        @if ($asgn->latitude && $asgn->longitude)
                                            <a href="https://maps.google.com/?q={{ $asgn->latitude }},{{ $asgn->longitude }}"
                                                target="_blank" style="color:var(--clr-lapangan);">
                                                {{ round($asgn->latitude, 5) }}, {{ round($asgn->longitude, 5) }}
                                                &nearr;
                                            </a>
                                        @else
                                            <span style="color:var(--clr-ink-3);">Belum Check-in</span>
                                        @endif
                                    @endif
                                </div>
                                <div>
                                    <span style="color:var(--clr-ink-3);">Harga Riil Lapangan:</span>
                                    @if ($asgn->harga_aktual)
                                        <strong style="color:var(--clr-accent);font-size:0.95rem;">Rp
                                            {{ number_format($asgn->harga_aktual, 0, ',', '.') }}</strong>
                                    @else
                                        <span style="color:var(--clr-ink-3);">Belum Input</span>
                                    @endif
                                </div>
                            </div>

                            @if ($asgn->catatan_temuan || $asgn->catatan_wawancara)
                                <div
                                    style="font-size:0.85rem;color:var(--clr-ink);margin-top:0.6rem;background:var(--clr-ground-3);padding:0.75rem;border-radius:4px;">
                                    @if ($asgn->catatan_temuan)
                                        <strong>Temuan Lapangan:</strong> {{ $asgn->catatan_temuan }}<br>
                                    @endif
                                    @if ($asgn->catatan_wawancara)
                                        <strong
                                            style="{{ $asgn->catatan_temuan ? 'margin-top:0.35rem;display:inline-block;' : '' }}">Hasil
                                            Wawancara:</strong> {{ $asgn->catatan_wawancara }}
                                    @endif
                                </div>
                            @endif

                            @if ($asgn->foto_bukti)
                                <div style="margin-top:0.75rem;">
                                    <div style="font-size:0.75rem;color:var(--clr-ink-3);margin-bottom:0.35rem;">Foto
                                        Bukti Lapangan (Watermarked):</div>
                                    <a href="{{ asset('storage/' . $asgn->foto_bukti) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $asgn->foto_bukti) }}" alt="Foto Lapangan"
                                            style="max-height:160px;border-radius:4px;border:1px solid var(--clr-border);">
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div style="color:var(--clr-ink-3);font-size:0.85rem;padding:1rem 0;">
                        Belum ada penugasan petugas untuk isu ini.
                    </div>
                @endif
            </div>

            {{-- TAHAP 4: INTERVENSI PASAR --}}
            <div class="sp-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
                    <div style="display:flex;align-items:center;gap:0.6rem;">
                        <span class="sp-badge badge-intervensi">Tahap 4</span>
                        <h2 style="font-size:1.1rem;font-weight:600;margin:0;">Langkah Intervensi & Koordinasi</h2>
                    </div>
                    @if (auth()->user()->isRole(['admin', 'super_admin']))
                        <a href="{{ route('intervensi.create', $issue) }}" class="sp-btn sp-btn-secondary sp-btn-sm">
                            + Tambah Intervensi
                        </a>
                    @endif
                </div>

                @if ($issue->interventions->count() > 0)
                    @foreach ($issue->interventions as $intv)
                        <div
                            style="background:var(--clr-ground-4);border-radius:6px;padding:1.25rem;margin-bottom:0.75rem;">
                            <div
                                style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;flex-wrap:wrap;gap:0.5rem;">
                                <strong style="font-size:0.95rem;color:var(--clr-ink);">
                                    {{ $intv->nama_intervensi }}
                                    ({{ strtoupper(str_replace('_', ' ', $intv->jenis_intervensi)) }})
                                </strong>
                                <span
                                    class="sp-badge {{ $intv->status === 'selesai' ? 'badge-selesai' : 'badge-intervensi' }}">
                                    {{ ucfirst(str_replace('_', ' ', $intv->status)) }}
                                </span>
                            </div>

                            <div style="font-size:0.85rem;color:var(--clr-ink-2);margin-bottom:0.5rem;">
                                {{ $intv->deskripsi }}
                            </div>

                            <div
                                style="display:grid;grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));gap:0.5rem;font-size:0.8rem;color:var(--clr-ink-3);">
                                <div><strong>PIC:</strong> {{ $intv->pic_nama }} ({{ $intv->pic_jabatan ?? '-' }})
                                </div>
                                <div><strong>Periode:</strong>
                                    {{ \Carbon\Carbon::parse($intv->tanggal_mulai)->format('d/m/Y') }} s/d
                                    {{ $intv->target_selesai ? \Carbon\Carbon::parse($intv->target_selesai)->format('d/m/Y') : 'Selesai' }}
                                </div>
                            </div>

                            @if ($intv->file_dokumen)
                                <div style="margin-top:0.6rem;">
                                    <a href="{{ Storage::url($intv->file_dokumen) }}" target="_blank"
                                        class="sp-btn sp-btn-secondary sp-btn-sm"
                                        style="font-size:0.8rem;display:inline-flex;align-items:center;gap:0.4rem;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                            <polyline points="10 9 9 9 8 9" />
                                        </svg>
                                        <span>Lihat Dokumen: {{ $intv->nama_dokumen ?: 'Dokumen Pendukung' }}</span>
                                    </a>
                                </div>
                            @endif

                            @if ($intv->progres_terakhir)
                                <div
                                    style="margin-top:0.6rem;padding:0.5rem;background:var(--clr-ground-3);border-radius:4px;font-size:0.8rem;color:var(--clr-ink);">
                                    <strong>Update Progres:</strong> {{ $intv->progres_terakhir }}
                                </div>
                            @endif

                            @if (auth()->user()->isRole(['admin', 'super_admin']) && $intv->status !== 'selesai')
                                <div
                                    style="margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid var(--clr-border);">
                                    <form method="POST" action="{{ route('intervensi.update-progres', $intv) }}"
                                        style="display:flex;gap:0.5rem;align-items:center;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="progres_terakhir"
                                            placeholder="Catat update progres terbaru..." required class="sp-input"
                                            style="padding:0.4rem 0.6rem;font-size:0.8rem;flex:1;">
                                        <select name="status" class="sp-select"
                                            style="padding:0.4rem 0.6rem;font-size:0.8rem;width:140px;">
                                            <option value="sedang_berjalan"
                                                {{ $intv->status === 'sedang_berjalan' ? 'selected' : '' }}>Sedang
                                                Berjalan</option>
                                            <option value="selesai">Selesai</option>
                                            <option value="dibatalkan">Dibatalkan</option>
                                        </select>
                                        <button type="submit"
                                            class="sp-btn sp-btn-secondary sp-btn-sm">Perbarui</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div style="color:var(--clr-ink-3);font-size:0.85rem;padding:1rem 0;">
                        Belum ada intervensi pasar yang dicatat.
                    </div>
                @endif
            </div>

            {{-- TAHAP 5: HASIL AKHIR & RESOLUSI --}}
            @if ($issue->resolution)
                <div class="sp-card sp-resolution-card">
                    <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1.25rem;">
                        <span class="sp-badge badge-selesai">Tahap 5</span>
                        <h2 style="font-size:1.1rem;font-weight:600;margin:0;">Hasil Akhir & Penutupan Isu</h2>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.25rem;">
                        <div style="background:var(--clr-ground-4);padding:1rem;border-radius:6px;">
                            <span class="sp-stat-label">Harga Sebelum Intervensi</span>
                            <div style="font-size:1.3rem;font-weight:700;color:var(--clr-kritis);">
                                Rp {{ number_format($issue->resolution->harga_sebelum_intervensi ?? 0, 0, ',', '.') }}
                            </div>
                        </div>

                        <div style="background:var(--clr-ground-4);padding:1rem;border-radius:6px;">
                            <span class="sp-stat-label">Harga Setelah Intervensi</span>
                            <div style="font-size:1.3rem;font-weight:700;color:var(--clr-selesai);">
                                Rp {{ number_format($issue->resolution->harga_setelah_intervensi ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div style="font-size:0.95rem;color:var(--clr-ink);margin-bottom:0.75rem;">
                        <strong>Kesimpulan & Evaluasi:</strong><br>
                        <p style="margin:0.35rem 0 0;line-height:1.5;color:var(--clr-ink-2);">
                            {{ $issue->resolution->kesimpulan }}
                        </p>
                    </div>

                    <div
                        style="display:flex;justify-content:space-between;font-size:0.8rem;color:var(--clr-ink-3);border-top:1px solid var(--clr-border);padding-top:0.75rem;">
                        <span>Ditutup Oleh:
                            <strong>{{ $issue->resolution->ditutupBy->name ?? 'Admin' }}</strong></span>
                        <span>Durasi Penanganan: <strong>{{ $issue->resolution->durasi_penanganan_hari }}
                                Hari</strong></span>
                    </div>
                </div>
            @endif

        </div>

        {{-- Right: Audit Trail & Summary Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Audit Trail / Activity Log --}}
            <div class="sp-card">
                <h3
                    style="font-size:0.95rem;font-weight:600;margin:0 0 1rem;display:flex;align-items:center;gap:0.5rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    Audit Trail (Riwayat Aktivitas)
                </h3>

                <div class="sp-timeline">
                    @forelse($issue->logs as $log)
                        <div class="sp-timeline-item">
                            <div class="sp-timeline-time">
                                {{ $log->created_at->format('d M Y, H:i') }} • {{ $log->user->name ?? 'Sistem' }}
                            </div>
                            <div class="sp-timeline-action">{{ $log->action }}</div>
                            <div class="sp-timeline-desc">{{ $log->deskripsi }}</div>
                        </div>
                    @empty
                        <div style="color:var(--clr-ink-3);font-size:0.8rem;">Belum ada log tercatat.</div>
                    @endforelse
                </div>
            </div>

            {{-- Status Rantai Komando --}}
            <div class="sp-card">
                <h3 style="font-size:0.95rem;font-weight:600;margin:0 0 1rem;">Rantai Komando Isu</h3>
                <div style="display:flex;flex-direction:column;gap:0.75rem;font-size:0.82rem;">
                    <div style="display:flex;justify-content:space-between;">
                        <span style="color:var(--clr-ink-3);">Pengawas:</span>
                        <strong style="color:var(--clr-ink);">{{ $issue->inputBy->name ?? '-' }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                        <span style="color:var(--clr-ink-3);">Admin Penugasan:</span>
                        <strong style="color:var(--clr-ink);">{{ $issue->assignBy->name ?? 'Belum' }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                        <span style="color:var(--clr-ink-3);">Petugas:</span>
                        <strong style="color:var(--clr-ink);">
                            {{ $issue->assignments->first()->petugas->name ?? 'Belum Ditugaskan' }}
                        </strong>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
