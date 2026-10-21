<x-app-layout>
    <x-slot name="title">Tugas & Laporan Lapangan</x-slot>



    {{-- Notifikasi Flash --}}
    @if(session('success'))
    <div class="sp-alert sp-alert-success" style="margin-bottom:1.25rem;">
        <div>{{ session('success') }}</div>
    </div>
    @endif
    @if(session('info'))
    <div class="sp-alert sp-alert-info" style="margin-bottom:1.25rem;">
        <div>{{ session('info') }}</div>
    </div>
    @endif
    @if(session('error'))
    <div class="sp-alert sp-alert-error" style="margin-bottom:1.25rem;">
        <div>{{ session('error') }}</div>
    </div>
    @endif

    {{-- Tab Navigasi: Pemisahan Status Selesai dan Belum Selesai --}}
    <div style="display:flex;gap:0.6rem;margin-bottom:1.5rem;border-bottom:1px solid var(--clr-border);padding-bottom:0.75rem;flex-wrap:wrap;">
        <a href="{{ route('laporan.index', ['tab' => 'belum_selesai']) }}"
           class="sp-btn {{ $tab === 'belum_selesai' ? 'sp-btn-primary' : 'sp-btn-secondary' }}"
           style="padding:0.45rem 1rem;font-size:0.85rem;border-radius:999px;display:inline-flex;align-items:center;gap:0.45rem;">
            <span>Tugas Belum Selesai</span>
            <span style="background:{{ $tab === 'belum_selesai' ? 'rgba(255,255,255,0.25)' : 'var(--clr-bg-subtle)' }};color:{{ $tab === 'belum_selesai' ? '#fff' : 'var(--clr-ink)' }};padding:0.12rem 0.5rem;border-radius:999px;font-size:0.72rem;font-weight:700;">
                {{ $totalBelumSelesai }}
            </span>
        </a>

        <a href="{{ route('laporan.index', ['tab' => 'selesai']) }}"
           class="sp-btn {{ $tab === 'selesai' ? 'sp-btn-primary' : 'sp-btn-secondary' }}"
           style="padding:0.45rem 1rem;font-size:0.85rem;border-radius:999px;display:inline-flex;align-items:center;gap:0.45rem;">
            <span>Tugas Sudah Selesai</span>
            <span style="background:{{ $tab === 'selesai' ? 'rgba(255,255,255,0.25)' : 'var(--clr-bg-subtle)' }};color:{{ $tab === 'selesai' ? '#fff' : 'var(--clr-ink)' }};padding:0.12rem 0.5rem;border-radius:999px;font-size:0.72rem;font-weight:700;">
                {{ $totalSelesai }}
            </span>
        </a>
    </div>

    {{-- KONTEN TAB: BELUM SELESAI --}}
    @if($tab === 'belum_selesai')

        {{-- 1. Tugas Yang Sedang Dikerjakan Petugas Ini (Jika Ada) --}}
        @if($myActiveAssignments->isNotEmpty())
        <div class="sp-card" style="margin-bottom:1.5rem;border-left:4px solid var(--clr-accent);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem;">
                <div>
                    <h2 style="font-size:1.05rem;font-weight:700;margin:0 0 0.2rem;color:var(--clr-accent);">
                        Tugas Sedang Saya Kerjakan
                    </h2>
                    <p style="font-size:0.8rem;color:var(--clr-ink-2);margin:0;">
                        Isu yang sudah Anda ambil dan siap untuk dilakukan check-in GPS serta laporan harga lapangan.
                    </p>
                </div>
                <span class="sp-badge badge-di_lapangan" style="font-size:0.78rem;">
                    {{ $myActiveAssignments->count() }} Sedang Berjalan
                </span>
            </div>

            <div class="sp-table-wrap" style="border:1px solid var(--clr-border);border-radius:8px;">
                <table class="sp-table">
                    <thead>
                        <tr>
                            <th>Kode Isu</th>
                            <th>Komoditas & Lokasi</th>
                            <th>Tanggal Diambil</th>
                            <th>Status Pemeriksaan</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myActiveAssignments as $myAsgn)
                        @php
                            $kabName = preg_replace('/^Kabupaten\s+/i', '', $myAsgn->issue->kabupaten->name ?? '-');
                            $detailLokasi = $myAsgn->issue->lokasi_spesifik ?: ($myAsgn->issue->pasar->name ?? null);
                        @endphp
                        <tr>
                            <td class="cell-code">
                                <a href="{{ route('laporan.show', $myAsgn) }}" style="color:var(--clr-accent);font-weight:700;">
                                    {{ $myAsgn->issue->kode_isu }}
                                </a>
                            </td>
                            <td>
                                <strong style="color:var(--clr-ink);">{{ $myAsgn->issue->komoditas->name }}</strong><br>
                                <span style="font-size:0.75rem;color:var(--clr-ink-3);">
                                    {{ $kabName }}{{ $detailLokasi ? ' • ' . $detailLokasi : '' }}
                                </span>
                            </td>
                            <td style="font-size:0.8rem;color:var(--clr-ink-3);white-space:nowrap;">
                                {{ \Carbon\Carbon::parse($myAsgn->tanggal_penugasan)->isoFormat('D MMM YYYY, HH:mm') }}
                            </td>
                            <td>
                                <span class="sp-badge {{ $myAsgn->status === 'sedang_dikerjakan' ? 'badge-di_lapangan' : 'badge-ditugaskan' }}">
                                    @if($myAsgn->checkin_at)
                                        {{ ($myAsgn->metode_verifikasi ?? 'offline') === 'online' ? 'Validasi Online' : 'Check-in GPS' }}
                                    @else
                                        Menunggu Validasi
                                    @endif
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('laporan.show', $myAsgn) }}" class="sp-btn sp-btn-primary sp-btn-sm" style="white-space:nowrap;">
                                    Lanjutkan Laporan &rarr;
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- 2. Semua Isu yang Telah Di-inputkan & Siap Dipilih Petugas --}}
        <div class="sp-card" style="padding:0;overflow:hidden;">
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--clr-border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.5rem;">
                <div>
                    <h2 style="font-size:1.1rem;font-weight:700;margin:0 0 0.2rem;color:var(--clr-ink);">
                        Daftar Isu Menunggu Pemeriksaan Lapangan
                    </h2>
                    <p style="font-size:0.82rem;color:var(--clr-ink-2);margin:0;">
                        Semua isu pangan hasil pantauan yang telah di-inputkan. Pilih isu yang berada di wilayah jangkauan Anda untuk dilakukan inspeksi fisik.
                    </p>
                </div>
                <span class="sp-badge badge-baru" style="font-size:0.8rem;padding:0.35rem 0.75rem;">
                    {{ $availableIssues->total() }} Isu Siap Dipilih
                </span>
            </div>

            <div class="sp-table-wrap" style="border:none;border-radius:0;">
                <table class="sp-table">
                    <thead>
                        <tr>
                            <th>Kode Isu</th>
                            <th>Komoditas & Lokasi</th>
                            <th>Ringkasan Isu</th>
                            <th>Prioritas</th>
                            <th>Waktu Temuan</th>
                            <th style="text-align:right;">Pilihan Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($availableIssues as $isu)
                        @php
                            $kabName = preg_replace('/^Kabupaten\s+/i', '', $isu->kabupaten->name ?? '-');
                            $detailLokasi = $isu->lokasi_spesifik ?: ($isu->pasar->name ?? null);
                        @endphp
                        <tr>
                            <td class="cell-code" style="vertical-align:top;padding-top:1rem;">
                                <a href="{{ route('issues.show', $isu) }}" style="color:var(--clr-accent);font-weight:600;">
                                    {{ $isu->kode_isu }}
                                </a>
                            </td>
                            <td style="vertical-align:top;padding-top:1rem;">
                                <strong style="color:var(--clr-ink);">{{ $isu->komoditas->name }}</strong><br>
                                <span style="font-size:0.75rem;color:var(--clr-ink-3);">
                                    {{ $kabName }}{{ $detailLokasi ? ' • ' . $detailLokasi : '' }}
                                </span>
                            </td>
                            <td style="vertical-align:top;max-width:280px;padding-top:1rem;">
                                <div style="font-size:0.82rem;color:var(--clr-ink);margin-bottom:0.25rem;">
                                    "{{ Str::limit($isu->kutipan_postingan, 75) }}"
                                </div>
                                <span style="font-size:0.72rem;color:var(--clr-ink-3);">
                                    Kategori: <strong style="color:var(--clr-ink-2);">{{ $isu->kategori_isu }}</strong>
                                </span>
                            </td>
                            <td style="vertical-align:top;padding-top:1rem;">
                                <span class="sp-badge badge-{{ $isu->prioritas }}">
                                    {{ ucfirst($isu->prioritas) }}
                                </span>
                            </td>
                            <td style="vertical-align:top;font-size:0.8rem;color:var(--clr-ink-3);white-space:nowrap;padding-top:1rem;">
                                {{ $isu->tanggal_temuan ? \Carbon\Carbon::parse($isu->tanggal_temuan)->isoFormat('D MMM YYYY') : '-' }}
                            </td>
                            <td style="vertical-align:middle;text-align:right;white-space:nowrap;">
                                <form method="POST" action="{{ route('laporan.ambil', $isu) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="sp-btn sp-btn-primary sp-btn-sm" style="white-space:nowrap;">
                                        Pilih & Kerjakan &rarr;
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:3rem 0;color:var(--clr-ink-3);">
                                <div style="font-size:1.1rem;font-weight:600;margin-bottom:0.25rem;">Tidak Ada Isu Tersedia</div>
                                <p style="font-size:0.85rem;margin:0;">Saat ini belum ada isu baru yang menunggu penugasan atau seluruh isu telah selesai diperiksa.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($availableIssues->hasPages())
            <div style="padding:1rem 1.5rem;border-top:1px solid var(--clr-border);">
                {{ $availableIssues->appends(['tab' => 'belum_selesai'])->links() }}
            </div>
            @endif
        </div>

    {{-- KONTEN TAB: SUDAH SELESAI --}}
    @else
        <div class="sp-card" style="padding:0;overflow:hidden;">
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--clr-border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.5rem;">
                <div>
                    <h2 style="font-size:1.1rem;font-weight:700;margin:0 0 0.2rem;color:var(--clr-ink);">
                        Daftar Laporan Lapangan Selesai
                    </h2>
                    <p style="font-size:0.82rem;color:var(--clr-ink-2);margin:0;">
                        Seluruh tugas inspeksi pasar yang telah tuntas Anda periksa, divalidasi dengan GPS, dan diinput harga riilnya.
                    </p>
                </div>
                <span class="sp-badge badge-selesai" style="font-size:0.8rem;padding:0.35rem 0.75rem;">
                    {{ $completedAssignments->total() }} Tugas Selesai
                </span>
            </div>

            <div class="sp-table-wrap" style="border:none;border-radius:0;">
                <table class="sp-table">
                    <thead>
                        <tr>
                            <th>Kode Isu</th>
                            <th>Komoditas & Lokasi</th>
                            <th>Tanggal Kunjungan</th>
                            <th>Harga Aktual Lapangan</th>
                            <th>Status</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($completedAssignments as $cAsgn)
                        @php
                            $kabName = preg_replace('/^Kabupaten\s+/i', '', $cAsgn->issue->kabupaten->name ?? '-');
                            $detailLokasi = $cAsgn->issue->lokasi_spesifik ?: ($cAsgn->issue->pasar->name ?? null);
                        @endphp
                        <tr>
                            <td class="cell-code">
                                <a href="{{ route('laporan.show', $cAsgn) }}" style="color:var(--clr-accent);font-weight:700;">
                                    {{ $cAsgn->issue->kode_isu }}
                                </a>
                            </td>
                            <td>
                                <strong style="color:var(--clr-ink);">{{ $cAsgn->issue->komoditas->name }}</strong><br>
                                <span style="font-size:0.75rem;color:var(--clr-ink-3);">
                                    {{ $kabName }}{{ $detailLokasi ? ' • ' . $detailLokasi : '' }}
                                </span>
                            </td>
                            <td style="font-size:0.8rem;color:var(--clr-ink-3);white-space:nowrap;">
                                {{ $cAsgn->tanggal_kunjungan ? \Carbon\Carbon::parse($cAsgn->tanggal_kunjungan)->isoFormat('D MMM YYYY') : '-' }}
                            </td>
                            <td>
                                @if($cAsgn->harga_aktual)
                                <div style="font-weight:700;color:var(--clr-ink);font-size:0.9rem;">
                                    Rp {{ number_format($cAsgn->harga_aktual, 0, ',', '.') }}
                                    <span style="font-size:0.75rem;font-weight:normal;color:var(--clr-ink-3);">/ {{ $cAsgn->issue->komoditas->satuan ?? 'kg' }}</span>
                                </div>
                                @if($cAsgn->issue->komoditas && $cAsgn->issue->komoditas->harga_referensi)
                                    @php $selisih = $cAsgn->harga_aktual - $cAsgn->issue->komoditas->harga_referensi; @endphp
                                    <span style="font-size:0.72rem;color:{{ $selisih > 0 ? '#b71c1c' : '#2e7d32' }};font-weight:600;">
                                        {{ $selisih > 0 ? '+' : '' }}Rp {{ number_format($selisih, 0, ',', '.') }} dari acuan
                                    </span>
                                @endif
                                @else
                                <span style="color:var(--clr-ink-3);font-size:0.8rem;">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="sp-badge badge-selesai">
                                    Selesai Dilaporkan ({{ ($cAsgn->metode_verifikasi ?? 'offline') === 'online' ? 'Online' : 'Offline' }})
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('laporan.show', $cAsgn) }}" class="sp-btn sp-btn-secondary sp-btn-sm" style="white-space:nowrap;">
                                    Lihat Laporan &rarr;
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:3rem 0;color:var(--clr-ink-3);">
                                <div style="font-size:1.1rem;font-weight:600;margin-bottom:0.25rem;">Belum Ada Laporan Selesai</div>
                                <p style="font-size:0.85rem;margin:0;">Laporan inspeksi yang telah Anda selesaikan akan tercatat pada tab ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($completedAssignments->hasPages())
            <div style="padding:1rem 1.5rem;border-top:1px solid var(--clr-border);">
                {{ $completedAssignments->appends(['tab' => 'selesai'])->links() }}
            </div>
            @endif
        </div>
    @endif
</x-app-layout>
