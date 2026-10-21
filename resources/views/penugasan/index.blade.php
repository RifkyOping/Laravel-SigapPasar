<x-app-layout>
    <x-slot name="title">Penugasan Petugas (Tahap 2)</x-slot>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 style="font-size:1.4rem;font-weight:700;margin:0 0 0.25rem;">Penugasan Petugas (Tahap 2)</h1>
            <p style="color:var(--clr-ink-2);font-size:0.85rem;margin:0;">
                Tugaskan petugas spesifik untuk melakukan inspeksi fisik, cek harga riil, dan dokumentasi GPS di lokasi pasar.
            </p>
        </div>
        <span class="sp-badge badge-ditugaskan" style="font-size:0.85rem;padding:0.4rem 0.8rem;">
            {{ $issues->total() }} Menunggu Penugasan
        </span>
    </div>

    @if($errors->any())
    <div class="sp-alert sp-alert-error" style="margin-bottom:1.5rem;">
        <div>
            <strong>Gagal menugaskan:</strong>
            <ul style="margin:0.5rem 0 0;padding-left:1.25rem;">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="sp-card" style="padding:0;overflow:hidden;">
        <div class="sp-table-wrap" style="border:none;border-radius:0;">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Kode Isu</th>
                        <th>Komoditas & Lokasi</th>
                        <th>Kutipan / Catatan Isu</th>
                        <th>Prioritas</th>
                        <th>Tugaskan Kepada & Catatan</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($issues as $isu)
                    <tr>
                        <td class="cell-code" style="vertical-align:top;padding-top:1.2rem;">
                            <a href="{{ route('issues.show', $isu) }}" style="color:var(--clr-accent);font-weight:600;">
                                {{ $isu->kode_isu }}
                            </a>
                        </td>
                        <td style="vertical-align:top;padding-top:1.2rem;">
                            <strong style="color:var(--clr-ink);">{{ $isu->komoditas->name }}</strong><br>
                            @php
                                $kabName = preg_replace('/^Kabupaten\s+/i', '', $isu->kabupaten->name ?? '-');
                                $detailLokasi = $isu->lokasi_spesifik ?: ($isu->pasar->name ?? null);
                            @endphp
                            <span style="font-size:0.75rem;color:var(--clr-ink-3);">{{ $kabName }}{{ $detailLokasi ? ' • ' . $detailLokasi : '' }}</span>
                        </td>
                        <td style="vertical-align:top;max-width:280px;padding-top:1.2rem;">
                            <div style="font-size:0.82rem;color:var(--clr-ink);margin-bottom:0.25rem;">
                                "{{ Str::limit($isu->kutipan_postingan, 70) }}"
                            </div>
                            @if($isu->catatan_input)
                            <span style="font-size:0.72rem;color:var(--clr-accent);">
                                Catatan: {{ Str::limit($isu->catatan_input, 60) }}
                            </span>
                            @endif
                        </td>
                        <td style="vertical-align:top;padding-top:1.2rem;">
                            <span class="sp-badge badge-{{ $isu->prioritas }}">
                                {{ ucfirst($isu->prioritas) }}
                            </span>
                        </td>
                        <td style="min-width:300px;">
                            <form id="form-assign-{{ $isu->id }}" method="POST" action="{{ route('penugasan.store', $isu) }}" style="display:flex;flex-direction:column;gap:0.5rem;">
                                @csrf
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;">
                                    <div>
                                        <label class="sp-label" style="font-size:0.68rem;">Pilih Petugas *</label>
                                        <select name="petugas_id" required class="sp-select" style="padding:0.4rem 0.6rem;font-size:0.8rem;">
                                            <option value="">-- Petugas --</option>
                                            @foreach($petugas as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->instansi ?? 'Petugas' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="sp-label" style="font-size:0.68rem;">Tanggal Tugas *</label>
                                        <input type="date" name="tanggal_penugasan" value="{{ now()->toDateString() }}" required class="sp-input" style="padding:0.4rem 0.6rem;font-size:0.8rem;">
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="catatan_admin" placeholder="Instruksi khusus admin (opsional)..." class="sp-input" style="padding:0.4rem 0.6rem;font-size:0.8rem;">
                                </div>
                            </form>
                        </td>
                        <td style="text-align:right;vertical-align:middle;">
                            <button type="submit" form="form-assign-{{ $isu->id }}" class="sp-btn sp-btn-primary sp-btn-sm">
                                Kirim Tugas &rarr;
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:3rem 0;color:var(--clr-ink-3);">
                            <div style="font-size:1.1rem;font-weight:600;margin-bottom:0.25rem;">Tidak Ada Isu Menunggu Penugasan</div>
                            <p style="font-size:0.85rem;margin:0;">Semua laporan telah diberikan instruksi penugasan lapangan.</p>
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
