<x-app-layout>
    <x-slot name="title">Edit Isu {{ $issue->kode_isu }}</x-slot>

    <div style="max-width:880px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('issues.show', $issue) }}" class="sp-btn sp-btn-ghost sp-btn-sm" style="margin-bottom:0.75rem;">
                &larr; Kembali ke Detail Isu
            </a>
            <h1 style="font-size:1.5rem;font-weight:700;margin:0 0 0.35rem;">Edit Data Isu ({{ $issue->kode_isu }})</h1>
            <p style="color:var(--clr-ink-2);font-size:0.88rem;margin:0;">
                Perbarui rincian deteksi siber atau informasi lokasi dan komoditas.
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

        <form method="POST" action="{{ route('issues.update', $issue) }}" class="sp-card" style="padding:2rem;">
            @csrf
            @method('PATCH')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Tanggal & Waktu Temuan <span class="required">*</span></label>
                    <input type="datetime-local" name="tanggal_temuan" value="{{ old('tanggal_temuan', $issue->tanggal_temuan->format('Y-m-d\TH:i')) }}" required class="sp-input">
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Platform Sumber <span class="required">*</span></label>
                    <select name="platform" required class="sp-select">
                        @foreach(['Twitter/X', 'TikTok', 'Instagram', 'Facebook', 'YouTube', 'WhatsApp', 'Online News', 'Lainnya'] as $plt)
                        <option value="{{ $plt }}" {{ old('platform', $issue->platform) === $plt ? 'selected' : '' }}>{{ $plt }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Tautan / Link Postingan Viral</label>
                    <input type="url" name="link_postingan" value="{{ old('link_postingan', $issue->link_postingan) }}" class="sp-input">
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Nama Akun Sumber</label>
                    <input type="text" name="nama_akun" value="{{ old('nama_akun', $issue->nama_akun) }}" class="sp-input">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Jumlah Interaksi <span class="required">*</span></label>
                    <input type="number" min="0" name="jumlah_interaksi" value="{{ old('jumlah_interaksi', $issue->jumlah_interaksi) }}" required class="sp-input">
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Jumlah Mention / Komentar</label>
                    <input type="number" min="0" name="jumlah_mention" value="{{ old('jumlah_mention', $issue->jumlah_mention) }}" class="sp-input">
                </div>
            </div>

            <div class="sp-form-group">
                <label class="sp-label">Kutipan / Transkrip Postingan <span class="required">*</span></label>
                <textarea name="kutipan_postingan" rows="3" required class="sp-textarea">{{ old('kutipan_postingan', $issue->kutipan_postingan) }}</textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Jenis Komoditas Pangan <span class="required">*</span></label>
                    <select name="komoditas_id" required class="sp-select">
                        @foreach($komoditas as $kom)
                        <option value="{{ $kom->id }}" {{ old('komoditas_id', $issue->komoditas_id) == $kom->id ? 'selected' : '' }}>
                            {{ $kom->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Kategori Isu <span class="required">*</span></label>
                    <select name="kategori_isu" required class="sp-select">
                        <option value="">-- Pilih kategori --</option>
                        @php
                            $currentKat = old('kategori_isu', $issue->kategori_isu);
                            $standardCats = [
                                'Kenaikan Harga',
                                'Kelangkaan / Stok Kosong',
                                'Kualitas Buruk',
                                'Masalah Distribusi',
                                'Pungli / Spekulasi'
                            ];
                        @endphp
                        @foreach($standardCats as $kat)
                        <option value="{{ $kat }}" {{ $currentKat === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                        {{-- Backward compatibility for older issues created with slug --}}
                        @if($currentKat && !in_array($currentKat, $standardCats))
                        <option value="{{ $currentKat }}" selected>{{ ucfirst(str_replace('_', ' ', $currentKat)) }}</option>
                        @endif
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Wilayah <span class="required">*</span></label>
                    <select name="kabupaten_id" required class="sp-select">
                        @foreach($kabupaten as $kab)
                        <option value="{{ $kab->id }}" {{ old('kabupaten_id', $issue->kabupaten_id) == $kab->id ? 'selected' : '' }}>{{ preg_replace('/^Kabupaten\s+/i', '', $kab->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Lokasi Spesifik</label>
                    <input type="text" name="lokasi_spesifik" value="{{ old('lokasi_spesifik', $issue->lokasi_spesifik ?? $issue->pasar?->name) }}" placeholder="Contoh: Pasar Baru Mamuju, Pasar Sentral, dll." class="sp-input">
                </div>
            </div>

            <div class="sp-form-group">
                <label class="sp-label">Catatan Tambahan Pengawas</label>
                <textarea name="catatan_input" rows="2" class="sp-textarea">{{ old('catatan_input', $issue->catatan_input) }}</textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:1rem;margin-top:2rem;">
                <a href="{{ route('issues.show', $issue) }}" class="sp-btn sp-btn-secondary">Batal</a>
                <button type="submit" class="sp-btn sp-btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
