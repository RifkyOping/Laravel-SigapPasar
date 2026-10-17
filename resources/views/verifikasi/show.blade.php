<x-app-layout>
    <x-slot name="title">Proses Verifikasi Isu {{ $issue->kode_isu }}</x-slot>

    <div style="max-width:960px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('verifikasi.index') }}" class="sp-btn sp-btn-ghost sp-btn-sm" style="margin-bottom:0.75rem;">
                &larr; Kembali ke Antrian Verifikasi
            </a>
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Verifikasi Isu Siber (Tahap 2)</h1>
                <span class="cell-code" style="font-size:1.1rem;">{{ $issue->kode_isu }}</span>
            </div>
            <p style="color:var(--clr-ink-2);font-size:0.88rem;margin:0.25rem 0 0;">
                Tentukan validitas laporan, skala prioritas penanganan, dan eliminasi isu duplikasi atau hoaks.
            </p>
        </div>

        @if($errors->any())
        <div class="sp-alert sp-alert-error" style="margin-bottom:1.5rem;">
            <div>
                <strong>Terdapat kesalahan pada input:</strong>
                <ul style="margin:0.5rem 0 0;padding-left:1.25rem;">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- Issue Details Summary Card --}}
        <div class="sp-card" style="margin-bottom:1.5rem;">
            <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;align-items:start;">
                <div>
                    <div style="font-size:0.75rem;color:var(--clr-ink-3);text-transform:uppercase;margin-bottom:0.35rem;letter-spacing:0.06em;">Kutipan Postingan Masyarakat:</div>
                    <blockquote style="margin:0 0 1rem;font-size:1.05rem;line-height:1.5;color:var(--clr-ink);background:var(--clr-ground-4);padding:1rem;border-radius:6px;border-left:3px solid var(--clr-accent);">
                        "{{ $issue->kutipan_postingan }}"
                    </blockquote>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;font-size:0.85rem;">
                        <div><span style="color:var(--clr-ink-3);">Platform:</span> <strong>{{ $issue->platform }}</strong></div>
                        <div><span style="color:var(--clr-ink-3);">Akun:</span> <strong>{{ $issue->nama_akun ?? '-' }}</strong></div>
                        <div><span style="color:var(--clr-ink-3);">Komoditas:</span> <strong style="color:var(--clr-accent);">{{ $issue->komoditas->name }}</strong></div>
                        @php
                            $detailLokasi = $issue->lokasi_spesifik ?: ($issue->pasar->name ?? null);
                        @endphp
                        <div><span style="color:var(--clr-ink-3);">Lokasi:</span> <strong>{{ $issue->kabupaten->name }}{{ $detailLokasi ? " ($detailLokasi)" : '' }}</strong></div>
                        <div><span style="color:var(--clr-ink-3);">Interaksi:</span> <strong>{{ number_format($issue->jumlah_interaksi) }}</strong></div>
                        <div><span style="color:var(--clr-ink-3);">Virality Score:</span> <strong style="color:var(--clr-ink);">{{ number_format($issue->virality_score) }}</strong></div>
                    </div>
                </div>

                <div>
                    @if($issue->evidences->where('tahap', 'input')->count() > 0)
                    <div style="font-size:0.75rem;color:var(--clr-ink-3);margin-bottom:0.35rem;">Bukti Screenshot:</div>
                    @foreach($issue->evidences->where('tahap', 'input') as $evi)
                        @if(str_contains($evi->file_type ?? '', 'image') || in_array(strtolower(pathinfo($evi->file_path, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp','gif']))
                        <a href="{{ asset('storage/' . $evi->file_path) }}" target="_blank">
                            <img src="{{ asset('storage/' . $evi->file_path) }}" alt="Screenshot" style="width:100%;max-height:180px;object-fit:cover;border-radius:6px;border:1px solid var(--clr-border);">
                        </a>
                        @else
                        <div style="padding:0.75rem;background:var(--clr-ground-2);border-radius:6px;border:1px solid var(--clr-border);margin-top:0.35rem;">
                            <a href="{{ asset('storage/' . $evi->file_path) }}" target="_blank" class="sp-btn sp-btn-ghost sp-btn-sm">
                                Unduh Dokumen ({{ $evi->file_name }})
                            </a>
                        </div>
                        @endif
                    @endforeach
                    @else
                    <div style="background:var(--clr-ground-4);padding:1.5rem;border-radius:6px;text-align:center;color:var(--clr-ink-3);font-size:0.8rem;">
                        Tidak ada screenshot dilampirkan
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Duplicate Issue Detection Banner --}}
        @if($issuesDuplikasi->count() > 0)
        <div class="sp-card" style="margin-bottom:1.5rem;border-color:rgba(245,158,11,0.3);background:rgba(245,158,11,0.03);">
            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;color:var(--clr-accent);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <strong style="font-size:0.95rem;">Potensi Isu Duplikasi Terdeteksi</strong>
            </div>
            <p style="font-size:0.83rem;color:var(--clr-ink-2);margin:0 0 0.75rem;">
                Ditemukan {{ $issuesDuplikasi->count() }} laporan aktif lainnya untuk komoditas <strong>{{ $issue->komoditas->name }}</strong> di wilayah <strong>{{ $issue->kabupaten->name }}</strong>. Jika laporan ini membahas isu yang sama, Anda dapat memilih hasil verifikasi "Duplikasi (Merge)".
            </p>
            <div style="display:flex;flex-direction:column;gap:0.4rem;font-size:0.8rem;">
                @foreach($issuesDuplikasi as $dup)
                <div style="background:var(--clr-ground-4);padding:0.5rem 0.75rem;border-radius:4px;display:flex;justify-content:space-between;align-items:center;">
                    <span><strong style="color:var(--clr-accent);">{{ $dup->kode_isu }}</strong> &mdash; "{{ Str::limit($dup->kutipan_postingan, 60) }}"</span>
                    <a href="{{ route('issues.show', $dup) }}" target="_blank" class="sp-btn sp-btn-ghost sp-btn-sm" style="padding:0.2rem 0.5rem;">Cek Isu</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Verification Form --}}
        <form method="POST" action="{{ route('verifikasi.store', $issue) }}" class="sp-card" style="padding:2rem;">
            @csrf

            <h2 style="font-size:1.15rem;font-weight:600;margin:0 0 1.25rem;">Keputusan Verifikator</h2>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                {{-- Status Validitas --}}
                <div class="sp-form-group">
                    <label class="sp-label">Hasil Verifikasi Keabsahan <span class="required">*</span></label>
                    <select name="hasil_verifikasi" id="hasil_verifikasi" required class="sp-select" onchange="toggleDuplikasi(this.value)">
                        <option value="">-- Tentukan Hasil --</option>
                        <option value="valid" {{ old('hasil_verifikasi') === 'valid' ? 'selected' : '' }}>Valid (Lolos Verifikasi)</option>
                        <option value="butuh_cek_lapangan" {{ old('hasil_verifikasi') === 'butuh_cek_lapangan' ? 'selected' : '' }}>Butuh Cek Lapangan</option>
                        <option value="hoaks" {{ old('hasil_verifikasi') === 'hoaks' ? 'selected' : '' }}>Hoaks / Tidak Benar</option>
                        <option value="duplikasi" {{ old('hasil_verifikasi') === 'duplikasi' ? 'selected' : '' }}>Duplikasi (Merge Issue)</option>
                    </select>
                </div>

                {{-- Skala Prioritas --}}
                <div class="sp-form-group">
                    <label class="sp-label">Skala Prioritas Penanganan <span class="required">*</span></label>
                    <select name="prioritas_rekomendasi" required class="sp-select">
                        <option value="">-- Pilih Prioritas --</option>
                        <option value="rendah" {{ old('prioritas_rekomendasi', $issue->prioritas) === 'rendah' ? 'selected' : '' }}>Rendah</option>
                        <option value="sedang" {{ old('prioritas_rekomendasi', $issue->prioritas) === 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="tinggi" {{ old('prioritas_rekomendasi', $issue->prioritas) === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                        <option value="kritis" {{ old('prioritas_rekomendasi', $issue->prioritas) === 'kritis' ? 'selected' : '' }}>Kritis (Lonjakan Ekstrem / Viral Tinggi)</option>
                    </select>
                </div>
            </div>

            {{-- Merge Target Selector --}}
            <div class="sp-form-group" id="merge_group" style="display:none;">
                <label class="sp-label">Gabungkan (Merge) ke Isu Utama</label>
                <select name="duplikasi_dari" class="sp-select">
                    <option value="">-- Pilih Isu Induk --</option>
                    @foreach($issuesDuplikasi as $dup)
                    <option value="{{ $dup->id }}">{{ $dup->kode_isu }} - {{ Str::limit($dup->kutipan_postingan, 50) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Catatan Verifikasi --}}
            <div class="sp-form-group">
                <label class="sp-label">Catatan Hasil Verifikasi <span class="required">*</span></label>
                <textarea name="catatan_verifikasi" rows="3" required placeholder="Jelaskan alasan penetapan status validitas, pengecekan sumber, dsb. (Minimal 10 karakter)..." class="sp-textarea">{{ old('catatan_verifikasi') }}</textarea>
            </div>

            {{-- Saran Tindakan --}}
            <div class="sp-form-group">
                <label class="sp-label">Saran Tindakan Lanjutan (Untuk Admin)</label>
                <textarea name="saran_tindakan" rows="2" placeholder="Contoh: Segera kirim petugas ke Pasar Wonokromo untuk cek harga grosir..." class="sp-textarea">{{ old('saran_tindakan') }}</textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:1rem;margin-top:2rem;">
                <a href="{{ route('verifikasi.index') }}" class="sp-btn sp-btn-secondary">Batal</a>
                <button type="submit" class="sp-btn sp-btn-primary">
                    Simpan & Teruskan ke Dashboard Admin &rarr;
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleDuplikasi(val) {
            const mg = document.getElementById('merge_group');
            if (val === 'duplikasi') {
                mg.style.display = 'block';
            } else {
                mg.style.display = 'none';
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('hasil_verifikasi');
            if (sel && sel.value === 'duplikasi') toggleDuplikasi('duplikasi');
        });
    </script>
</x-app-layout>
