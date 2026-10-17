<x-app-layout>
    <x-slot name="title">Daftar Pantauan Isu Pasar</x-slot>

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 style="font-size:1.4rem;font-weight:700;margin:0 0 0.25rem;">Pemantauan Isu Pasar & Media Siber</h1>
            <p style="color:var(--clr-ink-2);font-size:0.85rem;margin:0;">Laporan deteksi lonjakan harga, kelangkaan pasokan, dan temuan komoditas pangan</p>
        </div>

        @if(auth()->user()->isPengawasSiber() || auth()->user()->isRole(['admin','super_admin']))
        <a href="{{ route('issues.create') }}" class="sp-btn sp-btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Input Isu Baru (Tahap 1)
        </a>
        @endif
    </div>

    {{-- Filter Bar --}}
    <div class="sp-card" style="margin-bottom:1.5rem;padding:1.25rem;">
        <form method="GET" action="{{ route('issues.index') }}" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)) 80px;gap:0.75rem;align-items:flex-end;">
            <div>
                <label class="sp-label" style="font-size:0.7rem;">Cari Kata Kunci</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode isu, kutipan, akun..." class="sp-input" style="padding:0.5rem 0.75rem;font-size:0.85rem;">
            </div>

            <div>
                <label class="sp-label" style="font-size:0.7rem;">Status</label>
                <select name="status" class="sp-select" style="padding:0.5rem 0.75rem;font-size:0.85rem;">
                    <option value="">Semua Status</option>
                    @foreach(['baru' => 'Baru', 'diverifikasi' => 'Diverifikasi', 'ditugaskan' => 'Ditugaskan', 'di_lapangan' => 'Di Lapangan', 'laporan_selesai' => 'Laporan Lapangan Selesai', 'intervensi' => 'Intervensi', 'selesai' => 'Selesai', 'hoaks' => 'Hoaks', 'ditutup' => 'Ditutup'] as $key => $lbl)
                        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="sp-label" style="font-size:0.7rem;">Prioritas</label>
                <select name="prioritas" class="sp-select" style="padding:0.5rem 0.75rem;font-size:0.85rem;">
                    <option value="">Semua Prioritas</option>
                    @foreach(['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi', 'kritis' => 'Kritis'] as $key => $lbl)
                        <option value="{{ $key }}" {{ request('prioritas') === $key ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="sp-label" style="font-size:0.7rem;">Kabupaten/Kota</label>
                <select name="kabupaten" class="sp-select" style="padding:0.5rem 0.75rem;font-size:0.85rem;">
                    <option value="">Semua Wilayah</option>
                    @foreach($kabupaten as $kab)
                        <option value="{{ $kab->id }}" {{ request('kabupaten') == $kab->id ? 'selected' : '' }}>{{ $kab->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="sp-label" style="font-size:0.7rem;">Komoditas</label>
                <select name="komoditas" class="sp-select" style="padding:0.5rem 0.75rem;font-size:0.85rem;">
                    <option value="">Semua Komoditas</option>
                    @foreach($komoditas as $kom)
                        <option value="{{ $kom->id }}" {{ request('komoditas') == $kom->id ? 'selected' : '' }}>{{ $kom->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="sp-btn sp-btn-secondary" style="width:100%;justify-content:center;padding:0.5rem 0.75rem;font-size:0.85rem;">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Issues Table --}}
    <div class="sp-card" style="padding:0;overflow:hidden;">
        <div class="sp-table-wrap" style="border:none;border-radius:0;">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Kode Isu</th>
                        <th>Komoditas & Wilayah</th>
                        <th>Kutipan / Platform</th>
                        <th>Virality</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($issues as $isu)
                    <tr>
                        <td class="cell-code">
                            <a href="{{ route('issues.show', $isu) }}" style="color:var(--clr-accent);font-weight:600;">
                                {{ $isu->kode_isu }}
                            </a>
                        </td>
                        <td>
                            @php $detailLokasi = $isu->lokasi_spesifik ?: ($isu->pasar->name ?? null); @endphp
                            <span style="font-size:0.75rem;color:var(--clr-ink-3);">{{ $isu->kabupaten->name ?? '-' }}{{ $detailLokasi ? ' &bull; ' . $detailLokasi : '' }}</span>
                        </td>
                        <td style="max-width:280px;">
                            <div style="font-size:0.82rem;color:var(--clr-ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                "{{ $isu->kutipan_postingan }}"
                            </div>
                            <span style="font-size:0.72rem;color:var(--clr-ink-3);">
                                {{ $isu->platform }} &bull; {{ $isu->nama_akun ?? 'Anonim' }}
                            </span>
                        </td>
                        <td>
                            <span style="font-weight:700;color:var(--clr-ink);">{{ number_format($isu->virality_score) }}</span>
                            <div style="font-size:0.7rem;color:var(--clr-ink-3);">{{ number_format($isu->jumlah_interaksi) }} interaksi</div>
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
                        <td style="font-size:0.78rem;color:var(--clr-ink-3);white-space:nowrap;">
                            {{ $isu->tanggal_temuan->format('d/m/Y') }}
                        </td>
                        <td style="text-align:right;white-space:nowrap;">
                            <a href="{{ route('issues.show', $isu) }}" class="sp-btn sp-btn-ghost sp-btn-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:3rem 0;color:var(--clr-ink-3);">
                            <div style="font-size:1.1rem;font-weight:600;margin-bottom:0.25rem;">Tidak Ada Isu Ditemukan</div>
                            <p style="font-size:0.85rem;margin:0;">Cobalah mengubah filter pencarian atau input data isu baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($issues->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid var(--clr-border);">
            {{ $issues->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
