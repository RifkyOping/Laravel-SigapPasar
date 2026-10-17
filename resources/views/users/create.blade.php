<x-app-layout>
    <x-slot name="title">Tambah Pengguna Baru</x-slot>

    <div style="max-width:760px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('users.index') }}" class="sp-btn sp-btn-ghost sp-btn-sm" style="margin-bottom:0.75rem;">
                &larr; Kembali ke Manajemen Pengguna
            </a>
            <h1 style="font-size:1.5rem;font-weight:700;margin:0 0 0.35rem;">Tambah Akun Pengguna Baru</h1>
            <p style="color:var(--clr-ink-2);font-size:0.88rem;margin:0;">
                Buat akun baru dan tentukan peran wewenang dalam rantai komando penanganan isu.
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

        <form method="POST" action="{{ route('users.store') }}" class="sp-card" style="padding:2rem;">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama & Gelar" required class="sp-input">
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Alamat Email <span class="required">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@sigappasar.go.id" required class="sp-input">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Kata Sandi (Password) <span class="required">*</span></label>
                    <input type="password" name="password" minlength="8" required class="sp-input">
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Konfirmasi Kata Sandi <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" minlength="8" required class="sp-input">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Peran (Role Akses) <span class="required">*</span></label>
                    <select name="role" required class="sp-select">
                        <option value="">-- Pilih Peran --</option>
                        <option value="pimpinan" {{ old('role') === 'pimpinan' ? 'selected' : '' }}>Pimpinan (Ringkasan Eksekutif & Tren Harga)</option>
                        <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Admin (Operasional Dinas, Master & Akun)</option>
                        <option value="pengawas_siber" {{ old('role') === 'pengawas_siber' ? 'selected' : '' }}>Pengawas (Input Deteksi)</option>
                        <option value="verifikator" {{ old('role') === 'verifikator' ? 'selected' : '' }}>Verifikator (Verifikasi & Filter)</option>
                        <option value="petugas_lapangan" {{ old('role') === 'petugas_lapangan' ? 'selected' : '' }}>Petugas (Inspeksi Fisik & GPS)</option>
                    </select>
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Nomor Induk Pegawai (NIP)</label>
                    <input type="text" name="nip" value="{{ old('nip') }}" placeholder="19800101..." class="sp-input">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Instansi / Unit Kerja</label>
                    <input type="text" name="instansi" value="{{ old('instansi', 'Dinas Perdagangan') }}" placeholder="Dinas Perdagangan Kota Surabaya" class="sp-input">
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08123456789" class="sp-input">
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:1rem;margin-top:2rem;">
                <a href="{{ route('users.index') }}" class="sp-btn sp-btn-secondary">Batal</a>
                <button type="submit" class="sp-btn sp-btn-primary">
                    Simpan Akun Pengguna &rarr;
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
