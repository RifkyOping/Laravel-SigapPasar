<x-app-layout>
    <x-slot name="title">Tutup & Resolusi Isu {{ $issue->kode_isu }}</x-slot>

    <div style="max-width:880px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('issues.show', $issue) }}" class="sp-btn sp-btn-ghost sp-btn-sm" style="margin-bottom:0.75rem;">
                &larr; Kembali ke Detail Isu
            </a>
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Hasil Akhir & Penutupan Isu (Tahap 5)</h1>
                <span class="cell-code" style="font-size:1.1rem;">{{ $issue->kode_isu }}</span>
            </div>
            <p style="color:var(--clr-ink-2);font-size:0.88rem;margin:0.25rem 0 0;">
                Catat perbandingan harga sebelum vs sesudah intervensi, evaluasi efektivitas, dan tutup status isu secara resmi.
            </p>
        </div>

        @if($errors->any())
        <div class="sp-alert sp-alert-error" style="margin-bottom:1.5rem;">
            <div>
                <strong>Terdapat kesalahan:</strong>
                <ul style="margin:0.5rem 0 0;padding-left:1.25rem;">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- Ringkasan Riwayat Penanganan --}}
        <div class="sp-card" style="margin-bottom:1.5rem;">
            <h2 style="font-size:1.05rem;font-weight:600;margin:0 0 0.75rem;color:var(--clr-accent);">
                Ringkasan Penanganan Lapangan
            </h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1rem;font-size:0.85rem;">
                <div>
                    <span style="color:var(--clr-ink-3);">Komoditas:</span>
                    <div style="font-weight:600;color:var(--clr-ink);">{{ $issue->komoditas->name }}</div>
                    <span style="color:var(--clr-accent);font-size:0.78rem;">HET/Ref: Rp {{ number_format($issue->komoditas->harga_referensi, 0, ',', '.') }}</span>
                </div>

                <div>
                    <span style="color:var(--clr-ink-3);">Hasil Cek Petugas:</span>
                    @php $lastAsgn = $issue->assignments->last(); @endphp
                    @if($lastAsgn && $lastAsgn->harga_aktual)
                    <div style="font-weight:700;color:var(--clr-kritis);font-size:1rem;">
                        Rp {{ number_format($lastAsgn->harga_aktual, 0, ',', '.') }}
                    </div>
                    <span style="color:var(--clr-ink-3);font-size:0.75rem;">Oleh {{ $lastAsgn->petugas->name }}</span>
                    @else
                    <div style="color:var(--clr-ink-3);">Tidak ada data harga lapangan</div>
                    @endif
                </div>

                <div>
                    <span style="color:var(--clr-ink-3);">Intervensi Terlaksana:</span>
                    <div style="font-weight:600;color:var(--clr-ink);">{{ $issue->interventions->count() }} Kegiatan</div>
                    <span style="color:var(--clr-ink-3);font-size:0.75rem;">
                        {{ $issue->interventions->pluck('nama_intervensi')->implode(', ') ?: 'Tanpa intervensi khusus' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Form Resolusi --}}
        <form method="POST" action="{{ route('resolusi.store', $issue) }}" class="sp-card" style="padding:2rem;">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                {{-- Harga Sebelum Intervensi --}}
                <div class="sp-form-group">
                    <label class="sp-label">Harga Sebelum Intervensi (Rp)</label>
                    <input type="number" min="0" name="harga_sebelum_intervensi" value="{{ old('harga_sebelum_intervensi', $lastAsgn->harga_aktual ?? '') }}" placeholder="Contoh: 18500" class="sp-input">
                </div>

                {{-- Harga Setelah Intervensi --}}
                <div class="sp-form-group">
                    <label class="sp-label">Harga Setelah Intervensi / Normalisasi (Rp)</label>
                    <input type="number" min="0" name="harga_setelah_intervensi" value="{{ old('harga_setelah_intervensi') }}" placeholder="Contoh: 14000" class="sp-input">
                </div>
            </div>

            <div class="sp-form-group">
                <label class="sp-label">Status Akhir Penutupan <span class="required">*</span></label>
                <select name="status_akhir" required class="sp-select">
                    <option value="selesai" {{ old('status_akhir') === 'selesai' ? 'selected' : '' }}>Selesai Berhasil (Harga Stabil / Pasokan Pulih)</option>
                    <option value="ditutup_tanpa_intervensi" {{ old('status_akhir') === 'ditutup_tanpa_intervensi' ? 'selected' : '' }}>Ditutup Tanpa Intervensi (Kondisi Wajar di Lapangan)</option>
                    <option value="ditutup_hoaks" {{ old('status_akhir') === 'ditutup_hoaks' ? 'selected' : '' }}>Ditutup Karena Hoaks / Isu Usang</option>
                </select>
            </div>

            <div class="sp-form-group">
                <label class="sp-label">Kesimpulan Akhir & Catatan Evaluasi <span class="required">*</span></label>
                <textarea name="kesimpulan" rows="4" required placeholder="Tuliskan evaluasi komprehensif, penurunan harga yang tercapai, dan rekomendasi pemantauan berkala (Minimal 20 karakter)..." class="sp-textarea">{{ old('kesimpulan') }}</textarea>
            </div>

            <div class="sp-form-group">
                <label class="sp-label">Catatan Tambahan / Arsip Administrasi</label>
                <textarea name="catatan_akhir" rows="2" placeholder="Nomor surat dinas, arsip berita acara, dll..." class="sp-textarea">{{ old('catatan_akhir') }}</textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:1rem;margin-top:2rem;">
                <a href="{{ route('issues.show', $issue) }}" class="sp-btn sp-btn-secondary">Batal</a>
                <button type="submit" class="sp-btn sp-btn-primary" style="background:var(--clr-selesai);color:#fff;">
                    Tutup & Selesaikan Isu Resmi &check;
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
