<x-app-layout>
    <x-slot name="title">Laporan Lapangan {{ $assignment->issue->kode_isu }}</x-slot>

    <div style="max-width:920px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('laporan.index') }}" class="sp-btn sp-btn-ghost sp-btn-sm" style="margin-bottom:0.75rem;">
                &larr; Kembali ke Daftar Penugasan
            </a>
            <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
                <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Laporan Cek Fisik Lapangan (Tahap 3)</h1>
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
                    <span style="color:var(--clr-ink-3);">Wilayah & Lokasi:</span>
                    <div style="font-size:1rem;font-weight:600;color:var(--clr-ink);">{{ preg_replace('/^Kabupaten\s+/i', '', $assignment->issue->kabupaten->name ?? '-') }}</div>
                    @php $detailLokasi = $assignment->issue->lokasi_spesifik ?: ($assignment->issue->pasar->name ?? null); @endphp
                    @if($detailLokasi)
                    <span style="color:var(--clr-ink-2);font-size:0.8rem;">{{ $detailLokasi }}</span>
                    @endif
                </div>

                <div>
                    <span style="color:var(--clr-ink-3);">Catatan Penugasan ({{ $assignment->assignedBy?->name ?? 'Petugas Lapangan' }}):</span>
                    <div style="color:var(--clr-ink);margin-top:0.2rem;">{{ $assignment->catatan_admin ?? 'Pemeriksaan fisik harga dan ketersediaan komoditas di pasar terkait.' }}</div>
                </div>
            </div>
        </div>

        {{-- LANGKAH A: PILIHAN CEK LAPANGAN (OFFLINE VS ONLINE) --}}
        @php
            $currentMetode = old('metode_verifikasi', $assignment->metode_verifikasi ?? 'offline');
        @endphp
        <div class="sp-card" style="margin-bottom:1.5rem;border-left:3px solid var(--clr-lapangan);">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;flex-wrap:wrap;gap:0.75rem;">
                <div>
                    <h3 style="font-size:1.05rem;font-weight:600;margin:0;">Langkah 1: Metode Validasi Pemeriksaan</h3>
                    <p style="color:var(--clr-ink-2);font-size:0.8rem;margin:0.2rem 0 0;">
                        Pilih metode verifikasi: Cek fisik langsung di lokasi pasar (Offline) atau klarifikasi sumber informasi (Online).
                    </p>
                </div>

                @if($assignment->checkin_at)
                <span class="sp-badge badge-selesai">
                    @if($assignment->metode_verifikasi === 'online')
                        Sudah Validasi Online: {{ \Carbon\Carbon::parse($assignment->checkin_at)->format('H:i, d/m/Y') }}
                    @else
                        Sudah Check-in GPS: {{ \Carbon\Carbon::parse($assignment->checkin_at)->format('H:i, d/m/Y') }}
                    @endif
                </span>
                @endif
            </div>

            <form method="POST" action="{{ route('laporan.checkin', $assignment) }}">
                @csrf

                {{-- Pilihan 2 Metode: Offline vs Online --}}
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:1rem;margin-bottom:1.25rem;">
                    <label style="border:2px solid {{ $currentMetode === 'offline' ? 'var(--clr-accent)' : 'var(--clr-border)' }};border-radius:8px;padding:0.9rem 1.1rem;cursor:pointer;background:{{ $currentMetode === 'offline' ? 'var(--clr-bg-subtle)' : 'var(--clr-ground-2)' }};display:flex;align-items:center;gap:0.75rem;" id="card-metode-offline">
                        <input type="radio" name="metode_verifikasi" value="offline" id="opt-offline" {{ $currentMetode === 'offline' ? 'checked' : '' }} onchange="switchMetode('offline')">
                        <strong style="font-size:0.95rem;color:var(--clr-ink);">
                            Cek Lapangan Offline
                        </strong>
                    </label>

                    <label style="border:2px solid {{ $currentMetode === 'online' ? 'var(--clr-accent)' : 'var(--clr-border)' }};border-radius:8px;padding:0.9rem 1.1rem;cursor:pointer;background:{{ $currentMetode === 'online' ? 'var(--clr-bg-subtle)' : 'var(--clr-ground-2)' }};display:flex;align-items:center;gap:0.75rem;" id="card-metode-online">
                        <input type="radio" name="metode_verifikasi" value="online" id="opt-online" {{ $currentMetode === 'online' ? 'checked' : '' }} onchange="switchMetode('online')">
                        <strong style="font-size:0.95rem;color:var(--clr-ink);">
                            Cek Lapangan Online
                        </strong>
                    </label>
                </div>

                {{-- PANEL METODE OFFLINE (KOORDINAT GPS) --}}
                <div id="panel-offline" style="display:{{ $currentMetode === 'offline' ? 'block' : 'none' }};">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;flex-wrap:wrap;gap:0.5rem;">
                        <span style="font-size:0.82rem;font-weight:600;color:var(--clr-ink);">
                            Titik Koordinat Lokasi Pasar (GPS):
                        </span>
                        <button type="button" onclick="ambilLokasiGPS()" class="sp-btn sp-btn-secondary sp-btn-sm" style="border-color:var(--clr-lapangan);color:var(--clr-lapangan);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 00-8 8c0 5 8 12 8 12s8-7 8-12a8 8 0 00-8-8z"/></svg>
                            Deteksi GPS Otomatis
                        </button>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)) auto;gap:1rem;align-items:flex-end;">
                        <div class="sp-form-group" style="margin:0;">
                            <label class="sp-label" style="font-size:0.7rem;">Latitude <span class="required">*</span></label>
                            <input type="number" step="any" name="latitude" id="lat" value="{{ old('latitude', $assignment->latitude) }}" {{ $currentMetode === 'offline' ? 'required' : '' }} placeholder="-7.25747" class="sp-input" style="font-size:0.85rem;">
                        </div>
                        <div class="sp-form-group" style="margin:0;">
                            <label class="sp-label" style="font-size:0.7rem;">Longitude <span class="required">*</span></label>
                            <input type="number" step="any" name="longitude" id="lng" value="{{ old('longitude', $assignment->longitude) }}" {{ $currentMetode === 'offline' ? 'required' : '' }} placeholder="112.75209" class="sp-input" style="font-size:0.85rem;">
                        </div>
                        <div>
                            <button type="submit" class="sp-btn sp-btn-secondary" style="padding:0.65rem 1.25rem;white-space:nowrap;">
                                Simpan Check-in GPS
                            </button>
                        </div>
                    </div>
                    <div id="gps-status" style="font-size:0.75rem;color:var(--clr-accent);margin-top:0.5rem;display:none;"></div>
                </div>

                {{-- PANEL METODE ONLINE (NO TELEPON/WA / SOSMED SUMBER) --}}
                <div id="panel-online" style="display:{{ $currentMetode === 'online' ? 'block' : 'none' }};">
                    <div style="font-size:0.82rem;font-weight:600;color:var(--clr-ink);margin-bottom:0.75rem;">
                        Informasi Kontak & Akun Sumber (No. Telepon/WA atau Media Sosial):
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)) auto;gap:1rem;align-items:flex-end;">
                        <div class="sp-form-group" style="margin:0;">
                            <label class="sp-label" style="font-size:0.7rem;">No. Telepon / WhatsApp Sumber</label>
                            <input type="text" name="no_telepon_sumber" id="no_telp" value="{{ old('no_telepon_sumber', $assignment->no_telepon_sumber) }}" placeholder="Contoh: 081234567890" class="sp-input" style="font-size:0.85rem;">
                        </div>
                        <div class="sp-form-group" style="margin:0;">
                            <label class="sp-label" style="font-size:0.7rem;">Akun / Tautan Sosial Media Sumber</label>
                            <input type="text" name="sosmed_sumber" id="sosmed" value="{{ old('sosmed_sumber', $assignment->sosmed_sumber) }}" placeholder="Contoh: @akun_pedagang / link profil" class="sp-input" style="font-size:0.85rem;">
                        </div>
                        <div>
                            <button type="submit" class="sp-btn sp-btn-secondary" style="padding:0.65rem 1.25rem;white-space:nowrap;">
                                Simpan Verifikasi Online
                            </button>
                        </div>
                    </div>
                    <small style="color:var(--clr-ink-3);font-size:0.75rem;margin-top:0.5rem;display:block;">
                        * Masukkan nomor telepon/WhatsApp atau akun/tautan sosial media dari pihak/pedagang sumber informasi.
                    </small>
                </div>
            </form>
        </div>

        {{-- LANGKAH B: INPUT FORMULIR HASIL LAPANGAN --}}
        <form method="POST" action="{{ route('laporan.submit', $assignment) }}" enctype="multipart/form-data" class="sp-card" style="padding:2rem;">
            @csrf

            <h3 style="font-size:1.15rem;font-weight:600;margin:0 0 1.25rem;">Langkah 2: Laporan Temuan & Verifikasi Harga</h3>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                {{-- Harga Aktual --}}
                <div class="sp-form-group">
                    <label class="sp-label">Harga Aktual di Lapangan (Rp) <span class="required">*</span></label>
                    <input type="number" name="harga_aktual" min="0" value="{{ old('harga_aktual', $assignment->harga_aktual) }}" placeholder="Contoh: 18500" required class="sp-input">
                </div>

                {{-- Tanggal Kunjungan --}}
                <div class="sp-form-group">
                    <label class="sp-label">Tanggal Kunjungan / Pengecekan <span class="required">*</span></label>
                    <input type="date" name="tanggal_kunjungan" value="{{ old('tanggal_kunjungan', $assignment->tanggal_kunjungan ?? now()->toDateString()) }}" required class="sp-input">
                </div>
            </div>


            {{-- Catatan Wawancara --}}
            <div class="sp-form-group">
                <label class="sp-label">Catatan Hasil Wawancara</label>
                <textarea name="catatan_wawancara" rows="2" placeholder="Kutipan keterangan dari pedagang mengenai penyebab kenaikan (misal: pasokan dari distributor telat, cuaca buruk)..." class="sp-textarea">{{ old('catatan_wawancara', $assignment->catatan_wawancara) }}</textarea>
            </div>

            {{-- Upload Foto Lapangan --}}
            <div class="sp-form-group">
                <label class="sp-label">Foto Bukti Lapangan (Opsional)</label>
                <input type="file" name="foto_bukti" accept="image/*" class="sp-input" style="padding:0.5rem;">

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
        function switchMetode(metode) {
            const panelOffline = document.getElementById('panel-offline');
            const panelOnline = document.getElementById('panel-online');
            const cardOffline = document.getElementById('card-metode-offline');
            const cardOnline = document.getElementById('card-metode-online');
            const lat = document.getElementById('lat');
            const lng = document.getElementById('lng');

            if (metode === 'online') {
                panelOffline.style.display = 'none';
                panelOnline.style.display = 'block';
                cardOnline.style.borderColor = 'var(--clr-accent)';
                cardOnline.style.background = 'var(--clr-bg-subtle)';
                cardOffline.style.borderColor = 'var(--clr-border)';
                cardOffline.style.background = 'var(--clr-ground-2)';
                if (lat) lat.removeAttribute('required');
                if (lng) lng.removeAttribute('required');
            } else {
                panelOffline.style.display = 'block';
                panelOnline.style.display = 'none';
                cardOffline.style.borderColor = 'var(--clr-accent)';
                cardOffline.style.background = 'var(--clr-bg-subtle)';
                cardOnline.style.borderColor = 'var(--clr-border)';
                cardOnline.style.background = 'var(--clr-ground-2)';
                if (lat) lat.setAttribute('required', 'required');
                if (lng) lng.setAttribute('required', 'required');
            }
        }

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
