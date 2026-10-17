<x-app-layout>
    <x-slot name="title">Laporan Lapangan {{ $assignment->issue->kode_isu }}</x-slot>

    <div style="max-width:920px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('laporan.index') }}" class="sp-btn sp-btn-ghost sp-btn-sm" style="margin-bottom:0.75rem;">
                &larr; Kembali ke Daftar Penugasan
            </a>
            <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
                <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Laporan Cek Fisik Lapangan (Tahap 4)</h1>
                <span class="cell-code" style="font-size:1.1rem;">{{ $assignment->issue->kode_isu }}</span>
                <span class="sp-badge {{ $assignment->status === 'selesai' ? 'badge-selesai' : 'badge-ditugaskan' }}">
                    {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                </span>
            </div>
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

        {{-- Detail Target Penugasan --}}
        <div class="sp-card" style="margin-bottom:1.5rem;">
            <h2 style="font-size:1.05rem;font-weight:600;margin:0 0 1rem;color:var(--clr-accent);">
                Informasi Target Pengecekan
            </h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1rem;font-size:0.85rem;">
                <div>
                    <span style="color:var(--clr-ink-3);">Komoditas Sasaran:</span>
                    <div style="font-size:1rem;font-weight:600;color:var(--clr-ink);">{{ $assignment->issue->komoditas->name }}</div>
                    <span style="color:var(--clr-accent);font-size:0.78rem;">Harga Referensi: Rp {{ number_format($assignment->issue->komoditas->harga_referensi, 0, ',', '.') }}/{{ $assignment->issue->komoditas->satuan }}</span>
                </div>

                <div>
                    <span style="color:var(--clr-ink-3);">Wilayah & Pasar:</span>
                    <div style="font-size:1rem;font-weight:600;color:var(--clr-ink);">{{ $assignment->issue->kabupaten->name }}</div>
                    @php $detailLokasi = $assignment->issue->lokasi_spesifik ?: ($assignment->issue->pasar->name ?? null); @endphp
                    @if($detailLokasi)
                    <span style="color:var(--clr-ink-2);font-size:0.8rem;">{{ $detailLokasi }}</span>
                    @endif
                </div>

                <div>
                    <span style="color:var(--clr-ink-3);">Instruksi Admin ({{ $assignment->assignedBy->name }}):</span>
                    <div style="color:var(--clr-ink);margin-top:0.2rem;">{{ $assignment->catatan_admin ?? 'Cek harga riil dan penyebab lonjakan/kelangkaan.' }}</div>
                </div>
            </div>
        </div>

        {{-- LANGKAH A: CHECK-IN GPS LOKASI --}}
        <div class="sp-card" style="margin-bottom:1.5rem;border-left:3px solid var(--clr-lapangan);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.75rem;">
                <div>
                    <h3 style="font-size:1.05rem;font-weight:600;margin:0;">Langkah 1: Check-in Koordinat GPS Pasar</h3>
                    <p style="color:var(--clr-ink-2);font-size:0.8rem;margin:0.2rem 0 0;">
                        Wajib dilakukan di lokasi pasar untuk memvalidasi keberadaan petugas.
                    </p>
                </div>

                @if($assignment->checkin_at)
                <span class="sp-badge badge-selesai">
                    Sudah Check-in: {{ \Carbon\Carbon::parse($assignment->checkin_at)->format('H:i, d/m/Y') }}
                </span>
                @else
                <button type="button" onclick="ambilLokasiGPS()" class="sp-btn sp-btn-secondary" style="border-color:var(--clr-lapangan);color:var(--clr-lapangan);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 00-8 8c0 5 8 12 8 12s8-7 8-12a8 8 0 00-8-8z"/></svg>
                    Deteksi GPS Otomatis
                </button>
                @endif
            </div>

            <form method="POST" action="{{ route('laporan.checkin', $assignment) }}" style="display:grid;grid-template-columns:1fr 1fr auto;gap:1rem;align-items:flex-end;">
                @csrf
                <div class="sp-form-group" style="margin:0;">
                    <label class="sp-label" style="font-size:0.7rem;">Latitude <span class="required">*</span></label>
                    <input type="number" step="any" name="latitude" id="lat" value="{{ old('latitude', $assignment->latitude) }}" required placeholder="-7.25747" class="sp-input" style="font-size:0.85rem;">
                </div>
                <div class="sp-form-group" style="margin:0;">
                    <label class="sp-label" style="font-size:0.7rem;">Longitude <span class="required">*</span></label>
                    <input type="number" step="any" name="longitude" id="lng" value="{{ old('longitude', $assignment->longitude) }}" required placeholder="112.75209" class="sp-input" style="font-size:0.85rem;">
                </div>
                <div>
                    <button type="submit" class="sp-btn sp-btn-secondary" style="padding:0.65rem 1.25rem;">
                        Simpan Check-in
                    </button>
                </div>
            </form>
            <div id="gps-status" style="font-size:0.75rem;color:var(--clr-accent);margin-top:0.5rem;display:none;"></div>
        </div>

        {{-- LANGKAH B: INPUT FORMULIR HASIL LAPANGAN --}}
        <form method="POST" action="{{ route('laporan.submit', $assignment) }}" enctype="multipart/form-data" class="sp-card" style="padding:2rem;">
            @csrf

            <h3 style="font-size:1.15rem;font-weight:600;margin:0 0 1.25rem;">Langkah 2: Laporan Temuan Lapangan & Verifikasi Harga</h3>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                {{-- Harga Aktual --}}
                <div class="sp-form-group">
                    <label class="sp-label">Harga Aktual di Lapangan (Rp) <span class="required">*</span></label>
                    <input type="number" name="harga_aktual" min="0" value="{{ old('harga_aktual', $assignment->harga_aktual) }}" placeholder="Contoh: 18500" required class="sp-input">
                    <small style="color:var(--clr-ink-3);font-size:0.75rem;">
                        Harga referensi master: Rp {{ number_format($assignment->issue->komoditas->harga_referensi, 0, ',', '.') }}/{{ $assignment->issue->komoditas->satuan }}.
                    </small>
                </div>

                {{-- Tanggal Kunjungan --}}
                <div class="sp-form-group">
                    <label class="sp-label">Tanggal Kunjungan Lapangan <span class="required">*</span></label>
                    <input type="date" name="tanggal_kunjungan" value="{{ old('tanggal_kunjungan', $assignment->tanggal_kunjungan ?? now()->toDateString()) }}" required class="sp-input">
                </div>
            </div>

            {{-- Catatan Temuan Lapangan --}}
            <div class="sp-form-group">
                <label class="sp-label">Catatan Temuan Fisik & Kondisi Pasokan <span class="required">*</span></label>
                <textarea name="catatan_temuan" rows="3" required placeholder="Jelaskan kondisi stok, perbedaan harga antar pedagang, indikasi penimbunan, dsb. (Minimal 20 karakter)..." class="sp-textarea">{{ old('catatan_temuan', $assignment->catatan_temuan) }}</textarea>
            </div>

            {{-- Catatan Wawancara --}}
            <div class="sp-form-group">
                <label class="sp-label">Catatan Wawancara Singkat Pedagang / Distributor</label>
                <textarea name="catatan_wawancara" rows="2" placeholder="Kutipan keterangan dari pedagang mengenai penyebab kenaikan (misal: pasokan dari distributor telat, cuaca buruk)..." class="sp-textarea">{{ old('catatan_wawancara', $assignment->catatan_wawancara) }}</textarea>
            </div>

            {{-- Upload Foto Lapangan --}}
            <div class="sp-form-group">
                <label class="sp-label">Foto Bukti Lapangan Terkini (Dengan Timestamp/Watermark) <span class="required">*</span></label>
                <input type="file" name="foto_bukti" accept="image/*" {{ $assignment->foto_bukti ? '' : 'required' }} class="sp-input" style="padding:0.5rem;">
                <small style="color:var(--clr-ink-3);font-size:0.75rem;">Lampirkan foto kondisi lapak pasar/harga timbangan terkini.</small>

                @if($assignment->foto_bukti)
                <div style="margin-top:0.75rem;">
                    <span style="font-size:0.75rem;color:var(--clr-ink-3);">Foto yang sudah tersimpan:</span><br>
                    <a href="{{ asset('storage/' . $assignment->foto_bukti) }}" target="_blank">
                        <img src="{{ asset('storage/' . $assignment->foto_bukti) }}" alt="Foto Lapangan" style="max-height:140px;border-radius:4px;margin-top:0.35rem;border:1px solid var(--clr-border);">
                    </a>
                </div>
                @endif
            </div>

            <div style="display:flex;justify-content:flex-end;gap:1rem;margin-top:2rem;">
                <a href="{{ route('laporan.index') }}" class="sp-btn sp-btn-secondary">Kembali</a>
                <button type="submit" class="sp-btn sp-btn-primary">
                    Kirim Laporan Lapangan &rarr;
                </button>
            </div>
        </form>
    </div>

    <script>
        function ambilLokasiGPS() {
            const status = document.getElementById('gps-status');
            status.style.display = 'block';
            status.textContent = 'Mencari sinyal GPS perangkat...';

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        document.getElementById('lat').value = pos.coords.latitude.toFixed(6);
                        document.getElementById('lng').value = pos.coords.longitude.toFixed(6);
                        status.style.color = '#10B981';
                        status.textContent = `Lokasi GPS terdeteksi: ${pos.coords.latitude.toFixed(5)}, ${pos.coords.longitude.toFixed(5)} (Akurasi: ${Math.round(pos.coords.accuracy)}m)`;
                    },
                    (err) => {
                        status.style.color = '#EF4444';
                        status.textContent = 'Gagal mengakses GPS: ' + err.message + '. Silakan masukkan latitude & longitude secara manual.';
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            } else {
                status.style.color = '#EF4444';
                status.textContent = 'Peramban tidak mendukung GPS. Harap isi manual.';
            }
        }
    </script>
</x-app-layout>
