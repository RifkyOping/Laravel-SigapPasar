<x-app-layout>
    <x-slot name="title">Detail Isu {{ $issue->kode_isu }}</x-slot>

    {{-- Breadcrumb & Actions --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
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
                Terdeteksi pada {{ $issue->tanggal_temuan->isoFormat('D MMMM YYYY, HH:mm') }} &bull; Diinput oleh: <strong>{{ $issue->inputBy->name ?? 'Sistem' }}</strong>
            </p>
        </div>

        {{-- Contextual Action Buttons --}}
        <div style="display:flex;gap:0.6rem;align-items:center;flex-wrap:wrap;">
            {{-- Edit jika baru --}}
            @if(($issue->status === 'baru' && auth()->id() === $issue->input_by) || auth()->user()->isRole(['admin','super_admin']))
            <a href="{{ route('issues.edit', $issue) }}" class="sp-btn sp-btn-secondary sp-btn-sm">
                Edit Data
            </a>
            @endif

            {{-- Tahap 2: Verifikasi --}}
            @if($issue->status === 'baru' && auth()->user()->isRole(['verifikator','admin','super_admin']))
            <a href="{{ route('verifikasi.show', $issue) }}" class="sp-btn sp-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                Verifikasi Isu Ini
            </a>
            @endif

            {{-- Tahap 3: Penugasan Admin --}}
            @if($issue->status === 'diverifikasi' && auth()->user()->isRole(['admin','super_admin']))
            <a href="{{ route('penugasan.index') }}" class="sp-btn sp-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                Tugaskan Petugas
            </a>
            @endif

            {{-- Tahap 5: Intervensi --}}
            @if(in_array($issue->status, ['diverifikasi', 'ditugaskan', 'di_lapangan']) && auth()->user()->isRole(['admin','super_admin']))
            <a href="{{ route('intervensi.create', $issue) }}" class="sp-btn sp-btn-secondary">
                + Tambah Intervensi
            </a>
            @endif

            {{-- Tahap 6: Resolusi/Tutup Isu --}}
            @if(!in_array($issue->status, ['selesai', 'hoaks', 'ditutup']) && auth()->user()->isRole(['admin','super_admin']))
            <a href="{{ route('resolusi.create', $issue) }}" class="sp-btn sp-btn-primary">
                Tutup / Selesaikan Isu &rarr;
            </a>
            @endif
        </div>
    </div>

    {{-- Main 2-Column Content --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;align-items:start;">

        {{-- Left: The 6 Stages Workflow Details --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- TAHAP 1: DETEKSI & DATA AWAL --}}
            <div class="sp-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
                    <div style="display:flex;align-items:center;gap:0.6rem;">
                        <span class="sp-badge badge-baru">Tahap 1</span>
                        <h2 style="font-size:1.1rem;font-weight:600;margin:0;">Deteksi & Data Siber</h2>
                    </div>
                    <span style="font-size:0.8rem;color:var(--clr-ink-3);">Virality Score: <strong>{{ number_format($issue->virality_score) }}</strong></span>
                </div>

                <div style="background:var(--clr-ground-4);border-radius:6px;padding:1.25rem;margin-bottom:1.25rem;border-left:3px solid var(--clr-accent);">
                    <div style="font-size:0.75rem;color:var(--clr-ink-3);margin-bottom:0.4rem;text-transform:uppercase;letter-spacing:0.06em;">Kutipan Postingan Viral:</div>
                    <blockquote style="margin:0;font-size:1.05rem;line-height:1.5;color:var(--clr-ink);font-style:italic;">
                        "{{ $issue->kutipan_postingan }}"
                    </blockquote>
                </div>

                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1rem;margin-bottom:1.25rem;">
                    <div>
                        <div class="sp-stat-label">Platform Sumber</div>
                        <div style="font-size:0.95rem;font-weight:600;color:var(--clr-ink);">{{ $issue->platform }}</div>
                        <div style="font-size:0.8rem;color:var(--clr-ink-3);">Akun: {{ $issue->nama_akun ?? 'Tidak Disebutkan' }}</div>
                    </div>

                    <div>
                        <div class="sp-stat-label">Interaksi & Mention</div>
                        <div style="font-size:0.95rem;font-weight:600;color:var(--clr-ink);">{{ number_format($issue->jumlah_interaksi) }} Interaksi</div>
                        <div style="font-size:0.8rem;color:var(--clr-ink-3);">{{ number_format($issue->jumlah_mention) }} Mention/Komen</div>
                    </div>

                    <div>
                        <div class="sp-stat-label">Komoditas & Kategori</div>
                        <div style="font-size:0.95rem;font-weight:600;color:var(--clr-ink);">{{ $issue->komoditas->name ?? '-' }}</div>
                        <div style="font-size:0.8rem;color:var(--clr-accent);">{{ ucfirst(str_replace('_', ' ', $issue->kategori_isu)) }}</div>
                    </div>

                    <div>
                        <div class="sp-stat-label">Wilayah & Lokasi</div>
                        <div style="font-size:0.95rem;font-weight:600;color:var(--clr-ink);">{{ $issue->kabupaten->name ?? '-' }}</div>
                        @php $detailLokasi = $issue->lokasi_spesifik ?: ($issue->pasar->name ?? null); @endphp
                        @if($detailLokasi)
                        <div style="font-size:0.8rem;color:var(--clr-ink-3);">{{ $detailLokasi }}</div>
                        @endif
                    </div>
                </div>

                @if($issue->link_postingan)
                <div style="margin-bottom:1rem;font-size:0.85rem;">
                    <span style="color:var(--clr-ink-3);">Tautan Postingan:</span>
                    <a href="{{ $issue->link_postingan }}" target="_blank" rel="noopener" style="margin-left:0.4rem;text-decoration:underline;">
                        {{ $issue->link_postingan }} &nearr;
                    </a>
                </div>
                @endif

                @if($issue->evidences->where('tahap', 'input')->count() > 0)
                <div>
                    <div class="sp-stat-label" style="margin-bottom:0.5rem;">Bukti Tangkapan Layar:</div>
                    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                        @foreach($issue->evidences->where('tahap', 'input') as $evi)
                        <div style="border:1px solid var(--clr-border);border-radius:6px;overflow:hidden;background:var(--clr-ground-2);max-width:240px;">
                            @if(str_contains($evi->file_type ?? '', 'image') || in_array(strtolower(pathinfo($evi->file_path, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp','gif']))
                            <a href="{{ asset('storage/' . $evi->file_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $evi->file_path) }}" alt="Screenshot" style="width:100%;height:140px;object-fit:cover;">
                            </a>
                            @else
                            <div style="padding:1rem;text-align:center;">
                                <a href="{{ asset('storage/' . $evi->file_path) }}" target="_blank" class="sp-btn sp-btn-ghost sp-btn-sm">
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

            {{-- TAHAP 2: VERIFIKASI LAPORAN --}}
            <div class="sp-card">
                <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1.25rem;">
                    <span class="sp-badge badge-diverifikasi">Tahap 2</span>
                    <h2 style="font-size:1.1rem;font-weight:600;margin:0;">Verifikasi Laporan</h2>
                </div>

                @if($issue->verifications->count() > 0)
                    @foreach($issue->verifications as $v)
                    <div style="background:var(--clr-ground-4);border-radius:6px;padding:1.25rem;margin-bottom:0.75rem;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;flex-wrap:wrap;gap:0.5rem;">
                            <div>
                                <span class="sp-badge {{ $v->hasil_verifikasi === 'valid' ? 'badge-selesai' : ($v->hasil_verifikasi === 'hoaks' ? 'badge-hoaks' : 'badge-ditugaskan') }}">
                                    Hasil: {{ strtoupper(str_replace('_', ' ', $v->hasil_verifikasi)) }}
                                </span>
                                @if($v->prioritas_rekomendasi)
                                <span class="sp-badge badge-{{ $v->prioritas_rekomendasi }}" style="margin-left:0.4rem;">
                                    Rekomendasi: {{ ucfirst($v->prioritas_rekomendasi) }}
                                </span>
                                @endif
                            </div>
                            <span style="font-size:0.75rem;color:var(--clr-ink-3);">
                                Diverifikasi oleh: <strong>{{ $v->verifikator->name ?? 'Verifikator' }}</strong> &bull; {{ $v->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div style="font-size:0.9rem;color:var(--clr-ink);margin-bottom:0.5rem;">
                            <strong>Catatan Verifikator:</strong> {{ $v->catatan_verifikasi }}
                        </div>
                        @if($v->saran_tindakan)
                        <div style="font-size:0.85rem;color:var(--clr-ink-2);">
                            <strong>Saran Tindakan:</strong> {{ $v->saran_tindakan }}
                        </div>
                        @endif
                    </div>
                    @endforeach
                @else
                    <div style="color:var(--clr-ink-3);font-size:0.85rem;padding:1rem 0;">
                        Belum dilakukan verifikasi oleh tim verifikator.
                    </div>
                @endif
            </div>

            {{-- TAHAP 3 & 4: PENUGASAN & CEK LAPANGAN --}}
            <div class="sp-card">
                <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1.25rem;">
                    <span class="sp-badge badge-di_lapangan">Tahap 3 & 4</span>
                    <h2 style="font-size:1.1rem;font-weight:600;margin:0;">Penugasan Lapangan & Hasil Pengecekan Riil</h2>
                </div>

                @if($issue->assignments->count() > 0)
                    @foreach($issue->assignments as $asgn)
                    <div style="background:var(--clr-ground-4);border-radius:6px;padding:1.25rem;margin-bottom:1rem;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;flex-wrap:wrap;gap:0.5rem;">
                            <div>
                                <strong style="color:var(--clr-ink);font-size:0.95rem;">Petugas: {{ $asgn->petugas->name ?? '-' }}</strong>
                                <span style="font-size:0.8rem;color:var(--clr-ink-3);">({{ $asgn->petugas->phone ?? '-' }})</span>
                            </div>
                            <span class="sp-badge {{ $asgn->status === 'selesai' ? 'badge-selesai' : 'badge-ditugaskan' }}">
                                {{ ucfirst(str_replace('_', ' ', $asgn->status)) }}
                            </span>
                        </div>

                        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:0.75rem;margin-bottom:0.75rem;font-size:0.85rem;">
                            <div>
                                <span style="color:var(--clr-ink-3);">Ditugaskan Oleh:</span> {{ $asgn->assignedBy->name ?? 'Admin' }}
                            </div>
                            <div>
                                <span style="color:var(--clr-ink-3);">Tanggal Tugas:</span> {{ \Carbon\Carbon::parse($asgn->tanggal_penugasan)->format('d/m/Y') }}
                            </div>
                            <div>
                                <span style="color:var(--clr-ink-3);">Koordinat GPS:</span>
                                @if($asgn->latitude && $asgn->longitude)
                                <a href="https://maps.google.com/?q={{ $asgn->latitude }},{{ $asgn->longitude }}" target="_blank" style="color:var(--clr-lapangan);">
                                    {{ round($asgn->latitude, 5) }}, {{ round($asgn->longitude, 5) }} &nearr;
                                </a>
                                @else
                                <span style="color:var(--clr-ink-3);">Belum Check-in</span>
                                @endif
                            </div>
                            <div>
                                <span style="color:var(--clr-ink-3);">Harga Riil Lapangan:</span>
                                @if($asgn->harga_aktual)
                                <strong style="color:var(--clr-accent);font-size:0.95rem;">Rp {{ number_format($asgn->harga_aktual, 0, ',', '.') }}</strong>
                                @else
                                <span style="color:var(--clr-ink-3);">Belum Input</span>
                                @endif
                            </div>
                        </div>

                        @if($asgn->catatan_temuan)
                        <div style="font-size:0.85rem;color:var(--clr-ink);margin-top:0.6rem;background:var(--clr-ground-3);padding:0.75rem;border-radius:4px;">
                            <strong>Temuan Lapangan:</strong> {{ $asgn->catatan_temuan }}<br>
                            @if($asgn->catatan_wawancara)
                            <strong style="margin-top:0.35rem;display:inline-block;">Hasil Wawancara Pedagang:</strong> {{ $asgn->catatan_wawancara }}
                            @endif
                        </div>
                        @endif

                        @if($asgn->foto_bukti)
                        <div style="margin-top:0.75rem;">
                            <div style="font-size:0.75rem;color:var(--clr-ink-3);margin-bottom:0.35rem;">Foto Bukti Lapangan (Watermarked):</div>
                            <a href="{{ asset('storage/' . $asgn->foto_bukti) }}" target="_blank">
                                <img src="{{ asset('storage/' . $asgn->foto_bukti) }}" alt="Foto Lapangan" style="max-height:160px;border-radius:4px;border:1px solid var(--clr-border);">
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

            {{-- TAHAP 5: INTERVENSI PASAR --}}
            <div class="sp-card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
                    <div style="display:flex;align-items:center;gap:0.6rem;">
                        <span class="sp-badge badge-intervensi">Tahap 5</span>
                        <h2 style="font-size:1.1rem;font-weight:600;margin:0;">Langkah Intervensi & Koordinasi</h2>
                    </div>
                    @if(auth()->user()->isRole(['admin','super_admin']))
                    <a href="{{ route('intervensi.create', $issue) }}" class="sp-btn sp-btn-secondary sp-btn-sm">
                        + Tambah Intervensi
                    </a>
                    @endif
                </div>

                @if($issue->interventions->count() > 0)
                    @foreach($issue->interventions as $intv)
                    <div style="background:var(--clr-ground-4);border-radius:6px;padding:1.25rem;margin-bottom:0.75rem;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;flex-wrap:wrap;gap:0.5rem;">
                            <strong style="font-size:0.95rem;color:var(--clr-ink);">
                                {{ $intv->nama_intervensi }} ({{ strtoupper(str_replace('_', ' ', $intv->jenis_intervensi)) }})
                            </strong>
                            <span class="sp-badge {{ $intv->status === 'selesai' ? 'badge-selesai' : 'badge-intervensi' }}">
                                {{ ucfirst(str_replace('_', ' ', $intv->status)) }}
                            </span>
                        </div>

                        <div style="font-size:0.85rem;color:var(--clr-ink-2);margin-bottom:0.5rem;">
                            {{ $intv->deskripsi }}
                        </div>

                        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));gap:0.5rem;font-size:0.8rem;color:var(--clr-ink-3);">
                            <div><strong>PIC:</strong> {{ $intv->pic_nama }} ({{ $intv->pic_jabatan ?? '-' }})</div>
                            <div><strong>Periode:</strong> {{ \Carbon\Carbon::parse($intv->tanggal_mulai)->format('d/m/Y') }} s/d {{ $intv->target_selesai ? \Carbon\Carbon::parse($intv->target_selesai)->format('d/m/Y') : 'Selesai' }}</div>
                        </div>

                        @if($intv->progres_terakhir)
                        <div style="margin-top:0.6rem;padding:0.5rem;background:var(--clr-ground-3);border-radius:4px;font-size:0.8rem;color:var(--clr-ink);">
                            <strong>Update Progres:</strong> {{ $intv->progres_terakhir }}
                        </div>
                        @endif

                        @if(auth()->user()->isRole(['admin','super_admin']) && $intv->status !== 'selesai')
                        <div style="margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid var(--clr-border);">
                            <form method="POST" action="{{ route('intervensi.update-progres', $intv) }}" style="display:flex;gap:0.5rem;align-items:center;">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="progres_terakhir" placeholder="Catat update progres terbaru..." required class="sp-input" style="padding:0.4rem 0.6rem;font-size:0.8rem;flex:1;">
                                <select name="status" class="sp-select" style="padding:0.4rem 0.6rem;font-size:0.8rem;width:140px;">
                                    <option value="sedang_berjalan" {{ $intv->status === 'sedang_berjalan' ? 'selected' : '' }}>Sedang Berjalan</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="dibatalkan">Dibatalkan</option>
                                </select>
                                <button type="submit" class="sp-btn sp-btn-secondary sp-btn-sm">Perbarui</button>
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

            {{-- TAHAP 6: HASIL AKHIR & RESOLUSI --}}
            @if($issue->resolution)
            <div class="sp-card sp-resolution-card">
                <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1.25rem;">
                    <span class="sp-badge badge-selesai">Tahap 6</span>
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

                <div style="display:flex;justify-content:space-between;font-size:0.8rem;color:var(--clr-ink-3);border-top:1px solid var(--clr-border);padding-top:0.75rem;">
                    <span>Ditutup Oleh: <strong>{{ $issue->resolution->ditutupBy->name ?? 'Admin' }}</strong></span>
                    <span>Durasi Penanganan (SLA): <strong>{{ $issue->resolution->durasi_penanganan_hari }} Hari</strong></span>
                </div>
            </div>
            @endif

        </div>

        {{-- Right: Audit Trail & Summary Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">

            {{-- Audit Trail / Activity Log --}}
            <div class="sp-card">
                <h3 style="font-size:0.95rem;font-weight:600;margin:0 0 1rem;display:flex;align-items:center;gap:0.5rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Audit Trail (Riwayat Aktivitas)
                </h3>

                <div class="sp-timeline">
                    @forelse($issue->logs as $log)
                    <div class="sp-timeline-item">
                        <div class="sp-timeline-time">
                            {{ $log->created_at->format('d M Y, H:i') }} &bull; {{ $log->user->name ?? 'Sistem' }}
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
                        <span style="color:var(--clr-ink-3);">Verifikator:</span>
                        <strong style="color:var(--clr-ink);">{{ $issue->verifikasiBy->name ?? 'Belum' }}</strong>
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
