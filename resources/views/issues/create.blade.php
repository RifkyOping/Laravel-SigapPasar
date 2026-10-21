<x-app-layout>
    <x-slot name="title">Input Isu Baru (Tahap 1)</x-slot>

    <div style="max-width:880px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('issues.index') }}" class="sp-btn sp-btn-ghost sp-btn-sm" style="margin-bottom:0.75rem;">
                &larr; Kembali ke Daftar Isu
            </a>
            <h1 style="font-size:1.5rem;font-weight:700;margin:0 0 0.35rem;">Input Deteksi Isu Siber (Tahap 1)</h1>
            <p style="color:var(--clr-ink-2);font-size:0.88rem;margin:0;">
                Catat temuan viral dari media sosial mengenai lonjakan harga pangan, kelangkaan stok, atau dugaan kecurangan pasar.
            </p>
        </div>

        @if($errors->any())
        <div class="sp-alert sp-alert-error" style="margin-bottom:1.5rem;">
            <div>
                <strong>Terdapat kesalahan pada input formulir:</strong>
                <ul style="margin:0.5rem 0 0;padding-left:1.25rem;">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('issues.store') }}" enctype="multipart/form-data" class="sp-card" style="padding:2rem;">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                {{-- Tanggal Temuan --}}
                <div class="sp-form-group">
                    <label class="sp-label">Tanggal & Waktu Temuan <span class="required">*</span></label>
                    <input type="datetime-local" name="tanggal_temuan" value="{{ old('tanggal_temuan', now()->format('Y-m-d\TH:i')) }}" required class="sp-input">
                </div>

                {{-- Platform --}}
                <div class="sp-form-group">
                    <label class="sp-label">Platform Sumber <span class="required">*</span></label>
                    <select name="platform" required class="sp-select">
                        <option value="">-- Pilih Platform --</option>
                        @foreach(['Twitter/X', 'TikTok', 'Instagram', 'Facebook', 'YouTube', 'WhatsApp', 'Online News', 'Lainnya'] as $plt)
                        <option value="{{ $plt }}" {{ old('platform') === $plt ? 'selected' : '' }}>{{ $plt }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                {{-- Link Postingan --}}
                <div class="sp-form-group">
                    <label class="sp-label">Tautan / Link Postingan Viral</label>
                    <input type="url" name="link_postingan" value="{{ old('link_postingan') }}" placeholder="https://x.com/... atau https://tiktok.com/@..." class="sp-input">
                </div>

                {{-- Nama Akun --}}
                <div class="sp-form-group">
                    <label class="sp-label">Nama Akun / Kanal Sumber</label>
                    <input type="text" name="nama_akun" value="{{ old('nama_akun') }}" placeholder="@infomojokerto / Warga Surabaya" class="sp-input">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                {{-- Jumlah Interaksi --}}
                <div class="sp-form-group">
                    <label class="sp-label">Jumlah Interaksi (Likes + Shares) <span class="required">*</span></label>
                    <input type="number" min="0" name="jumlah_interaksi" value="{{ old('jumlah_interaksi', 0) }}" required class="sp-input">
                    <small style="color:var(--clr-ink-3);font-size:0.75rem;">Mempengaruhi kalkulasi virality score sistem.</small>
                </div>

                {{-- Jumlah Mention --}}
                <div class="sp-form-group">
                    <label class="sp-label">Jumlah Mention / Komentar</label>
                    <input type="number" min="0" name="jumlah_mention" value="{{ old('jumlah_mention', 0) }}" class="sp-input">
                </div>
            </div>

            {{-- Kutipan Postingan --}}
            <div class="sp-form-group">
                <label class="sp-label">Ringkasan Isu <span class="required">*</span></label>
                <textarea name="kutipan_postingan" rows="3" required placeholder="Tuliskan isi keluhan masyarakat atau kutipan narasi postingan..." class="sp-textarea">{{ old('kutipan_postingan') }}</textarea>
            </div>

            <hr style="border:none;border-top:1px solid var(--clr-border);margin:1.75rem 0;">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                {{-- Komoditas --}}
                <div class="sp-form-group">
                    <label class="sp-label">Jenis Komoditas Pangan <span class="required">*</span></label>
                    <select name="komoditas_id" required class="sp-select">
                        <option value="">-- Pilih Komoditas --</option>
                        @foreach($komoditas as $kom)
                        <option value="{{ $kom->id }}" {{ old('komoditas_id') == $kom->id ? 'selected' : '' }}>
                            {{ $kom->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Kategori Isu --}}
                <div class="sp-form-group">
                    <label class="sp-label">Kategori Isu <span class="required">*</span></label>
                    <select name="kategori_isu" required class="sp-select">
                        <option value="">-- Pilih kategori --</option>
                        @foreach([
                            'Kenaikan Harga',
                            'Kelangkaan / Stok Kosong',
                            'Kualitas Buruk',
                            'Masalah Distribusi',
                            'Pungli / Spekulasi'
                        ] as $kat)
                        <option value="{{ $kat }}" {{ old('kategori_isu') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                {{-- Kabupaten/Kota --}}
                <div class="sp-form-group">
                    <label class="sp-label">Wilayah <span class="required">*</span></label>
                    <select name="kabupaten_id" required class="sp-select">
                        <option value="">-- Pilih Wilayah --</option>
                        @foreach($kabupaten as $kab)
                        <option value="{{ $kab->id }}" {{ old('kabupaten_id') == $kab->id ? 'selected' : '' }}>{{ preg_replace('/^Kabupaten\s+/i', '', $kab->name) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Lokasi Spesifik --}}
                <div class="sp-form-group">
                    <label class="sp-label">Lokasi Spesifik</label>
                    <input type="text" name="lokasi_spesifik" value="{{ old('lokasi_spesifik') }}" placeholder="Contoh: Pasar Baru Mamuju, Pasar Sentral, dll." class="sp-input">
                </div>
            </div>

            {{-- Bukti Screenshot --}}
            <div class="sp-form-group">
                <label class="sp-label">Bukti Screenshot / Dokumen Pendukung</label>
                <input type="file" name="screenshot" accept="image/*,application/pdf" class="sp-input" style="padding:0.5rem;">
                <small style="color:var(--clr-ink-3);font-size:0.75rem;">Format diperbolehkan: JPG, PNG, WEBP, PDF (Maks. 10MB).</small>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:1rem;margin-top:2rem;">
                <a href="{{ route('issues.index') }}" class="sp-btn sp-btn-secondary">Batal</a>
                <button type="submit" class="sp-btn sp-btn-primary">
                    Simpan & Laporkan Isu &rarr;
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
