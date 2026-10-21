<x-app-layout>
    <x-slot name="title">Intervensi & Tindak Lanjut Pasar (Tahap 4)</x-slot>

    {{-- Header Halaman --}}
    <div
        style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.75rem;flex-wrap:wrap;gap:1.25rem;">
        <div>
            <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.35rem;">
                <span class="sp-badge badge-intervensi" style="font-size:0.75rem;padding:0.25rem 0.6rem;">Tahap 4</span>
                <h1 style="font-size:1.5rem;font-weight:700;margin:0;color:var(--clr-ink);">Intervensi & Tindak Lanjut
                    Pasar</h1>
            </div>
            <p style="color:var(--clr-ink-2);font-size:0.88rem;margin:0;max-width:720px;line-height:1.5;">
                Eksekusi langkah stabilisasi pangan seperti Operasi Pasar Murah, Sidak Pergudangan / Distributor, dan
                Koordinasi Pasokan Bulog di 6 kabupaten se-Sulawesi Barat.
            </p>
        </div>

        {{-- Ringkasan Statistik Singkat --}}
        <div style="display:flex;gap:0.75rem;align-items:center;">
            <div
                style="background:var(--clr-ground-3);border:1px solid var(--clr-border);border-radius:8px;padding:0.6rem 1rem;text-align:right;">
                <div style="font-size:0.7rem;color:var(--clr-ink-3);text-transform:uppercase;letter-spacing:0.06em;">
                    Menunggu Tindakan</div>
                <div
                    style="font-size:1.35rem;font-weight:700;color:{{ $antrianIsu->total() > 0 ? 'var(--clr-kritis)' : 'var(--clr-ink)' }};">
                    {{ $antrianIsu->total() }} Isu
                </div>
            </div>
            <div
                style="background:var(--clr-ground-3);border:1px solid var(--clr-border);border-radius:8px;padding:0.6rem 1rem;text-align:right;">
                <div style="font-size:0.7rem;color:var(--clr-ink-3);text-transform:uppercase;letter-spacing:0.06em;">
                    Intervensi Tercatat</div>
                <div style="font-size:1.35rem;font-weight:700;color:var(--clr-accent);">
                    {{ $interventions->total() }} Kegiatan
                </div>
            </div>
        </div>
    </div>

    {{-- Notifikasi Sukses / Info --}}
    @if (session('success'))
        <div class="sp-alert sp-alert-success" style="margin-bottom:1.5rem;">
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- Tab Navigasi 2-Tahap --}}
    <div
        style="display:flex;gap:0.75rem;border-bottom:2px solid var(--clr-border);margin-bottom:1.5rem;padding-bottom:0.25rem;">
        <a href="{{ route('intervensi.index', ['tab' => 'antrian']) }}"
            style="text-decoration:none;display:inline-flex;align-items:center;gap:0.6rem;padding:0.75rem 1.25rem;border-radius:8px 8px 0 0;font-size:0.92rem;font-weight:700;transition:all 0.15s ease;
                  {{ $activeTab === 'antrian'
                      ? 'color:#ffffff;background:var(--clr-primary, #2e7d32);border-bottom:3px solid var(--clr-primary-dark, #1b5e20);box-shadow:0 2px 4px rgba(0,0,0,0.1);'
                      : 'color:var(--clr-ink-2);background:var(--clr-ground-2);border:1px solid transparent;' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
            </svg>
            <span>Antrian Menunggu Intervensi</span>
            <span class="sp-badge {{ $antrianIsu->total() > 0 ? 'badge-kritis' : 'badge-sedang' }}"
                style="font-size:0.72rem;padding:0.15rem 0.5rem;border-radius:999px;{{ $activeTab === 'antrian' ? 'background:rgba(255,255,255,0.25);color:#fff;' : '' }}">
                {{ $antrianIsu->total() }}
            </span>
        </a>

        <a href="{{ route('intervensi.index', ['tab' => 'berjalan']) }}"
            style="text-decoration:none;display:inline-flex;align-items:center;gap:0.6rem;padding:0.75rem 1.25rem;border-radius:8px 8px 0 0;font-size:0.92rem;font-weight:700;transition:all 0.15s ease;
                  {{ $activeTab === 'berjalan'
                      ? 'color:#ffffff;background:var(--clr-primary, #2e7d32);border-bottom:3px solid var(--clr-primary-dark, #1b5e20);box-shadow:0 2px 4px rgba(0,0,0,0.1);'
                      : 'color:var(--clr-ink-2);background:var(--clr-ground-2);border:1px solid transparent;' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
            </svg>
            <span>Intervensi Sedang Berjalan & Selesai</span>
            <span class="sp-badge badge-intervensi"
                style="font-size:0.72rem;padding:0.15rem 0.5rem;border-radius:999px;{{ $activeTab === 'berjalan' ? 'background:rgba(255,255,255,0.25);color:#fff;' : '' }}">
                {{ $interventions->total() }}
            </span>
        </a>
    </div>

    {{-- KONTEN TAB 1: ANTRIAN MENUNGGU INTERVENSI --}}
    @if ($activeTab === 'antrian')
        <div
            style="background:var(--clr-ground-3);border:1px solid var(--clr-border);border-left:4px solid var(--clr-accent);border-radius:8px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
            <div>
                <strong style="color:var(--clr-ink);font-size:0.95rem;">Antrian Siap Tindak Lanjut Tahap 4</strong>
                <p style="color:var(--clr-ink-2);font-size:0.83rem;margin:0.2rem 0 0;">
                    Menampilkan isu pasar yang telah divalidasi atau telah selesai dicek fisik di lapangan oleh petugas,
                    namun belum memiliki tindakan intervensi. Klik tombol <strong>"Buat Intervensi"</strong> untuk
                    meluncurkan tindakan nyata.
                </p>
            </div>
            <span style="font-size:0.8rem;color:var(--clr-ink-3);">
                Total: <strong>{{ $antrianIsu->total() }}</strong> Isu
            </span>
        </div>

        <div class="sp-card" style="padding:0;overflow:hidden;">
            <div class="sp-table-wrap" style="border:none;border-radius:0;">
                <table class="sp-table">
                    <thead>
                        <tr>
                            <th style="width:140px;">Kode Isu</th>
                            <th style="width:200px;">Komoditas & Wilayah</th>
                            <th>Temuan Lapangan / Verifikasi</th>
                            <th style="width:130px;">Virality</th>
                            <th style="width:110px;">Prioritas</th>
                            <th style="text-align:right;width:180px;">Aksi Eksekusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($antrianIsu as $isu)
                            @php
                                $latestAssignment =
                                    $isu->assignments->where('status', 'selesai')->first() ??
                                    $isu->assignments->first();
                                $hargaRef = $isu->komoditas->harga_referensi ?? 0;
                                $hargaAktual = $latestAssignment?->harga_aktual ?? null;
                                $selisih = $hargaAktual && $hargaRef > 0 ? $hargaAktual - $hargaRef : 0;
                                $pctKenaikan =
                                    $hargaRef > 0 && $selisih > 0 ? round(($selisih / $hargaRef) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="cell-code">
                                    <a href="{{ route('issues.show', $isu) }}"
                                        style="color:var(--clr-accent);font-weight:700;text-decoration:none;">
                                        {{ $isu->kode_isu }}
                                    </a>
                                    <div style="margin-top:0.35rem;">
                                        <span class="sp-badge badge-{{ $isu->status }}" style="font-size:0.65rem;">
                                            {{ $isu->status_label }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <strong
                                        style="color:var(--clr-ink);font-size:0.95rem;">{{ $isu->komoditas->name ?? '-' }}</strong>
                                    <div style="font-size:0.78rem;color:var(--clr-ink-3);margin-top:0.25rem;">
                                        {{ preg_replace('/^Kabupaten\s+/i', '', $isu->kabupaten->name ?? '-') }}
                                        @if ($isu->lokasi_spesifik || $isu->pasar?->name)
                                            &bull; <em>{{ $isu->lokasi_spesifik ?? $isu->pasar->name }}</em>
                                        @endif
                                    </div>
                                    <div style="font-size:0.75rem;color:var(--clr-ink-2);margin-top:0.15rem;">
                                        Kategori:
                                        <strong>{{ ucfirst(str_replace('_', ' ', $isu->kategori_isu)) }}</strong>
                                    </div>
                                </td>

                                <td>
                                    @if ($latestAssignment && $latestAssignment->status === 'selesai' && $hargaAktual)
                                        <div
                                            style="display:flex;align-items:baseline;gap:0.5rem;margin-bottom:0.25rem;">
                                            <span style="font-size:0.78rem;color:var(--clr-ink-3);">Harga Riil:</span>
                                            <strong style="color:var(--clr-kritis);font-size:1rem;">
                                                Rp {{ number_format($hargaAktual, 0, ',', '.') }}
                                            </strong>
                                            <span style="font-size:0.75rem;color:var(--clr-ink-3);">
                                                (Ref: Rp {{ number_format($hargaRef, 0, ',', '.') }})
                                            </span>
                                            @if ($pctKenaikan > 0)
                                                <span class="sp-badge badge-kritis" style="font-size:0.65rem;">
                                                    +{{ $pctKenaikan }}%
                                                </span>
                                            @endif
                                        </div>
                                        <div style="font-size:0.8rem;color:var(--clr-ink);line-height:1.4;">
                                            <strong>Temuan:</strong>
                                            {{ Str::limit($latestAssignment->catatan_temuan, 85) }}
                                        </div>
                                        <div style="font-size:0.73rem;color:var(--clr-ink-3);margin-top:0.2rem;">
                                            Petugas: {{ $latestAssignment->petugas->name ?? '-' }} &bull; Cek:
                                            {{ \Carbon\Carbon::parse($latestAssignment->tanggal_kunjungan)->format('d/m/Y') }}
                                        </div>
                                    @elseif($latestAssignment)
                                        <div
                                            style="display:flex;align-items:center;gap:0.4rem;color:var(--clr-ditugaskan);font-size:0.82rem;font-weight:600;">
                                            <span class="sp-badge badge-ditugaskan" style="font-size:0.65rem;">Sedang
                                                Cek Fisik</span>
                                            <span>Petugas: {{ $latestAssignment->petugas->name ?? '-' }}</span>
                                        </div>
                                        <div style="font-size:0.78rem;color:var(--clr-ink-3);margin-top:0.2rem;">
                                            Tugas diberikan sejak
                                            {{ \Carbon\Carbon::parse($latestAssignment->tanggal_penugasan)->format('d/m/Y') }}.
                                            Anda dapat langsung mengintervensi jika kondisi mendesak.
                                        </div>
                                    @else
                                        <div style="font-size:0.82rem;color:var(--clr-verifikasi);font-weight:600;">
                                            Telah Diverifikasi Valid
                                        </div>
                                        <div style="font-size:0.78rem;color:var(--clr-ink-3);margin-top:0.2rem;">
                                            "{{ Str::limit($isu->kutipan_postingan, 75) }}"
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <span style="font-weight:700;color:var(--clr-ink);font-size:0.95rem;">
                                        {{ number_format($isu->virality_score) }}
                                    </span>
                                    <div style="font-size:0.72rem;color:var(--clr-ink-3);">
                                        {{ number_format($isu->jumlah_interaksi) }} interaksi
                                    </div>
                                    <div style="font-size:0.7rem;color:var(--clr-ink-3);">
                                        {{ $isu->platform }}
                                    </div>
                                </td>

                                <td>
                                    <span class="sp-badge badge-{{ $isu->prioritas }}" style="font-size:0.7rem;">
                                        {{ ucfirst($isu->prioritas) }}
                                    </span>
                                    <div style="font-size:0.72rem;color:var(--clr-ink-3);margin-top:0.25rem;">
                                        {{ $isu->tanggal_temuan->format('d/m/Y') }}
                                    </div>
                                </td>

                                <td style="text-align:right;">
                                    <div style="display:flex;flex-direction:column;gap:0.4rem;align-items:flex-end;">
                                        <a href="{{ route('intervensi.create', $isu) }}"
                                            class="sp-btn sp-btn-primary sp-btn-sm"
                                            style="font-weight:700;padding:0.45rem 0.85rem;background:var(--clr-primary, #2e7d32);">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.5">
                                                <line x1="12" y1="5" x2="12" y2="19" />
                                                <line x1="5" y1="12" x2="19" y2="12" />
                                            </svg>
                                            Buat Intervensi
                                        </a>
                                        <a href="{{ route('issues.show', $isu) }}"
                                            class="sp-btn sp-btn-ghost sp-btn-sm" style="font-size:0.78rem;">
                                            Detail Isu &rarr;
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    style="text-align:center;padding:3.5rem 1rem;color:var(--clr-ink-3);">
                                    <div
                                        style="width:48px;height:48px;border-radius:50%;background:rgba(46,125,50,0.1);color:var(--clr-primary, #2e7d32);display:inline-flex;align-items:center;justify-content:center;margin-bottom:0.75rem;">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                    </div>
                                    <div
                                        style="font-size:1.1rem;font-weight:700;color:var(--clr-ink);margin-bottom:0.35rem;">
                                        Tidak Ada Antrian Intervensi
                                    </div>
                                    <p
                                        style="font-size:0.85rem;margin:0;max-width:520px;display:inline-block;line-height:1.5;">
                                        Seluruh isu pasar yang terverifikasi dan telah dilaporkan oleh petugas sudah
                                        ditindaklanjuti dengan intervensi atau telah diselesaikan.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($antrianIsu->hasPages())
                <div style="padding:1rem 1.5rem;border-top:1px solid var(--clr-border);">
                    {{ $antrianIsu->appends(['tab' => 'antrian'])->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- KONTEN TAB 2: INTERVENSI SEDANG BERJALAN & SELESAI --}}
    @if ($activeTab === 'berjalan')
        <div
            style="background:var(--clr-ground-3);border:1px solid var(--clr-border);border-left:4px solid var(--clr-intervensi);border-radius:8px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
            <div>
                <strong style="color:var(--clr-ink);font-size:0.95rem;">Monitoring Pelaksanaan Intervensi
                    Lapangan</strong>
                <p style="color:var(--clr-ink-2);font-size:0.83rem;margin:0.2rem 0 0;">
                    Pantau progres operasi pasar murah, sidak gudang, dan distribusi sembako yang sedang berjalan.
                    Lakukan pembaruan berkala atau tutup isu jika harga telah stabil.
                </p>
            </div>
            <span style="font-size:0.8rem;color:var(--clr-ink-3);">
                Total: <strong>{{ $interventions->total() }}</strong> Kegiatan
            </span>
        </div>

        <div class="sp-card" style="padding:0;overflow:hidden;">
            <div class="sp-table-wrap" style="border:none;border-radius:0;">
                <table class="sp-table">
                    <thead>
                        <tr>
                            <th style="width:130px;">Kode Isu</th>
                            <th>Intervensi & Jenis</th>
                            <th>Komoditas & Wilayah</th>
                            <th>PIC / Penanggung Jawab</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Progres Terakhir</th>
                            <th style="text-align:right;width:160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interventions as $intv)
                            <tr>
                                <td class="cell-code">
                                    <a href="{{ route('issues.show', $intv->issue) }}"
                                        style="color:var(--clr-accent);font-weight:700;text-decoration:none;">
                                        {{ $intv->issue->kode_isu }}
                                    </a>
                                </td>
                                <td>
                                    <strong style="color:var(--clr-ink);">{{ $intv->nama_intervensi }}</strong><br>
                                    <span class="sp-badge badge-intervensi"
                                        style="font-size:0.65rem;margin-top:0.2rem;">
                                        {{ strtoupper(str_replace('_', ' ', $intv->jenis_intervensi)) }}
                                    </span>
                                    @if ($intv->file_dokumen)
                                        <div style="margin-top:0.3rem;">
                                            <a href="{{ Storage::url($intv->file_dokumen) }}" target="_blank"
                                                style="display:inline-flex;align-items:center;gap:0.3rem;font-size:0.75rem;color:var(--clr-accent);text-decoration:none;"
                                                title="Lihat/Unduh Dokumen">
                                                <svg width="12" height="12" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                                    <polyline points="14 2 14 8 20 8" />
                                                </svg>
                                                <span>{{ Str::limit($intv->nama_dokumen ?: 'Dokumen', 22) }}</span>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong
                                        style="color:var(--clr-ink);">{{ $intv->issue->komoditas->name }}</strong><br>
                                    <span style="font-size:0.75rem;color:var(--clr-ink-3);">
                                        {{ preg_replace('/^Kabupaten\s+/i', '', $intv->issue->kabupaten->name ?? '-') }}
                                        @if ($intv->issue->lokasi_spesifik || $intv->issue->pasar?->name)
                                            &bull; {{ $intv->issue->lokasi_spesifik ?? $intv->issue->pasar->name }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size:0.85rem;color:var(--clr-ink);font-weight:500;">
                                        {{ $intv->pic_nama }}</div>
                                    <small
                                        style="color:var(--clr-ink-3);">{{ $intv->pic_jabatan ?? 'Dinas Terkait' }}</small>
                                </td>
                                <td style="font-size:0.8rem;color:var(--clr-ink-3);white-space:nowrap;">
                                    {{ \Carbon\Carbon::parse($intv->tanggal_mulai)->format('d/m/Y') }} &ndash;
                                    {{ $intv->target_selesai ? \Carbon\Carbon::parse($intv->target_selesai)->format('d/m/Y') : 'Selesai' }}
                                </td>
                                <td>
                                    <span
                                        class="sp-badge {{ $intv->status === 'selesai' ? 'badge-selesai' : ($intv->status === 'sedang_berjalan' ? 'badge-intervensi' : 'badge-ditugaskan') }}">
                                        {{ ucfirst(str_replace('_', ' ', $intv->status)) }}
                                    </span>
                                </td>
                                <td style="max-width:220px;font-size:0.78rem;color:var(--clr-ink-2);">
                                    {{ Str::limit($intv->progres_terakhir ?? 'Belum ada update progres.', 55) }}
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:flex;flex-direction:column;gap:0.35rem;align-items:flex-end;">
                                        <a href="{{ route('issues.show', $intv->issue) }}"
                                            class="sp-btn sp-btn-ghost sp-btn-sm" style="font-size:0.78rem;">
                                            Detail Isu &rarr;
                                        </a>
                                        @if ($intv->status === 'sedang_berjalan')
                                            <a href="{{ route('resolusi.create', $intv->issue) }}"
                                                class="sp-btn sp-btn-secondary sp-btn-sm"
                                                style="font-size:0.75rem;padding:0.25rem 0.6rem;">
                                                Selesaikan Isu &rarr;
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8"
                                    style="text-align:center;padding:3.5rem 1rem;color:var(--clr-ink-3);">
                                    <div
                                        style="font-size:1.1rem;font-weight:700;color:var(--clr-ink);margin-bottom:0.25rem;">
                                        Belum Ada Intervensi Berjalan</div>
                                    <p style="font-size:0.85rem;margin:0;">Langkah intervensi akan tercatat ketika
                                        admin menindaklanjuti isu yang telah masuk dalam antrian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($interventions->hasPages())
                <div style="padding:1rem 1.5rem;border-top:1px solid var(--clr-border);">
                    {{ $interventions->appends(['tab' => 'berjalan'])->links() }}
                </div>
            @endif
        </div>
    @endif
</x-app-layout>
