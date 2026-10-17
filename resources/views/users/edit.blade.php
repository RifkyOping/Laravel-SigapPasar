<x-app-layout>
    <x-slot name="title">Edit Pengguna {{ $user->name }}</x-slot>

    <div style="max-width:760px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('users.index') }}" class="sp-btn sp-btn-ghost sp-btn-sm" style="margin-bottom:0.75rem;">
                &larr; Kembali ke Manajemen Pengguna
            </a>
            <h1 style="font-size:1.5rem;font-weight:700;margin:0 0 0.35rem;">Edit Pengguna: {{ $user->name }}</h1>
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

        <form method="POST" action="{{ route('users.update', $user) }}" class="sp-card" style="padding:2rem;">
            @csrf
            @method('PATCH')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="sp-input">
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Alamat Email <span class="required">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="sp-input">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Ubah Kata Sandi (Kosongkan jika tidak diganti)</label>
                    <input type="password" name="password" minlength="8" placeholder="Minimal 8 karakter" class="sp-input">
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" minlength="8" class="sp-input">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Peran (Role Akses) <span class="required">*</span></label>
                    <select name="role" required class="sp-select">
                        <option value="pimpinan" {{ old('role', $user->role) === 'pimpinan' ? 'selected' : '' }}>Pimpinan (Ringkasan Eksekutif & Tren Harga)</option>
                        <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Admin (Operasional Dinas, Master & Akun)</option>
                        <option value="pengawas_siber" {{ old('role', $user->role) === 'pengawas_siber' ? 'selected' : '' }}>Pengawas</option>
                        <option value="verifikator" {{ old('role', $user->role) === 'verifikator' ? 'selected' : '' }}>Verifikator</option>
                        <option value="petugas_lapangan" {{ old('role', $user->role) === 'petugas_lapangan' ? 'selected' : '' }}>Petugas</option>
                    </select>
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Nomor Induk Pegawai (NIP)</label>
                    <input type="text" name="nip" value="{{ old('nip', $user->nip) }}" class="sp-input">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Instansi / Unit Kerja</label>
                    <input type="text" name="instansi" value="{{ old('instansi', $user->instansi) }}" class="sp-input">
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="sp-input">
                </div>
            </div>

            <div class="sp-form-group">
                <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;color:var(--clr-ink);cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    Status Akun Aktif
                </label>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:1rem;margin-top:2rem;">
                <a href="{{ route('users.index') }}" class="sp-btn sp-btn-secondary">Batal</a>
                <button type="submit" class="sp-btn sp-btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
