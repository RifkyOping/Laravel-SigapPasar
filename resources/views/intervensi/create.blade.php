<x-app-layout>
    <x-slot name="title">Tambah Intervensi Pasar {{ $issue->kode_isu }}</x-slot>

    <div style="max-width:880px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('issues.show', $issue) }}" class="sp-btn sp-btn-ghost sp-btn-sm" style="margin-bottom:0.75rem;">
                &larr; Kembali ke Detail Isu
            </a>
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Rencana Intervensi & Tindak Lanjut (Tahap 4)</h1>
                <span class="cell-code" style="font-size:1.1rem;">{{ $issue->kode_isu }}</span>
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

        <form method="POST" action="{{ route('intervensi.store', $issue) }}" enctype="multipart/form-data" class="sp-card" style="padding:2rem;">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                {{-- Jenis Intervensi --}}
                <div class="sp-form-group">
                    <label class="sp-label">Jenis Tindakan Intervensi <span class="required">*</span></label>
                    <select name="jenis_intervensi" required class="sp-select">
                        <option value="">-- Pilih Jenis Intervensi --</option>
                        <option value="operasi_pasar" {{ old('jenis_intervensi') === 'operasi_pasar' ? 'selected' : '' }}>Operasi Pasar Murah</option>
                        <option value="sidak_gudang" {{ old('jenis_intervensi') === 'sidak_gudang' ? 'selected' : '' }}>Sidak Pergudangan / Distributor</option>
                        <option value="koordinasi_bulog" {{ old('jenis_intervensi') === 'koordinasi_bulog' ? 'selected' : '' }}>Koordinasi Pasokan Bulog</option>
                        <option value="rapat_koordinasi" {{ old('jenis_intervensi') === 'rapat_koordinasi' ? 'selected' : '' }}>Rapat Koordinasi Satgas Pangan</option>
                        <option value="penyuluhan" {{ old('jenis_intervensi') === 'penyuluhan' ? 'selected' : '' }}>Penyuluhan & Teguran Pedagang</option>
                        <option value="lainnya" {{ old('jenis_intervensi') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                {{-- Nama Intervensi --}}
                <div class="sp-form-group">
                    <label class="sp-label">Nama Kegiatan Intervensi <span class="required">*</span></label>
                    <input type="text" name="nama_intervensi" value="{{ old('nama_intervensi') }}" placeholder="Contoh: Operasi Beras Murah Pasar Wonokromo" required class="sp-input">
                </div>
            </div>

            {{-- Ringkasan Hasil Intervensi --}}
            <div class="sp-form-group">
                <label class="sp-label">Ringkasan Hasil Intervensi <span class="required">*</span></label>
                <textarea name="deskripsi" rows="3" required placeholder="Jelaskan ringkasan hasil kegiatan intervensi, volume komoditas, atau catatan tindak lanjut..." class="sp-textarea">{{ old('deskripsi') }}</textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                {{-- Tanggal Mulai --}}
                <div class="sp-form-group">
                    <label class="sp-label">Tanggal Mulai <span class="required">*</span></label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', now()->toDateString()) }}" required class="sp-input">
                </div>

                {{-- Target Selesai --}}
                <div class="sp-form-group">
                    <label class="sp-label">Target Tanggal Selesai</label>
                    <input type="date" name="target_selesai" value="{{ old('target_selesai', now()->addDays(3)->toDateString()) }}" class="sp-input">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                {{-- Nama PIC --}}
                <div class="sp-form-group">
                    <label class="sp-label">Nama Penanggung Jawab (PIC) <span class="required">*</span></label>
                    <input type="text" name="pic_nama" value="{{ old('pic_nama', auth()->user()->name) }}" required class="sp-input">
                </div>

                {{-- Jabatan PIC --}}
                <div class="sp-form-group">
                    <label class="sp-label">Jabatan PIC</label>
                    <input type="text" name="pic_jabatan" value="{{ old('pic_jabatan') }}" placeholder="Kabid Pengendalian Perdagangan Dalam Negeri" class="sp-input">
                </div>
            </div>

            {{-- Dokumen Pendukung (PDF / Word Opsional) --}}
            <div class="sp-form-group" style="margin-top:1.25rem;">
                <label class="sp-label" for="dokumen_pendukung">
                    Dokumen Pendukung
                </label>
                <input type="file" 
                       id="dokumen_pendukung" 
                       name="dokumen_pendukung" 
                       accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" 
                       class="sp-input"
                       style="padding:8px 12px;cursor:pointer;">
            </div>

            <div style="display:flex;justify-content:flex-end;gap:1rem;margin-top:2rem;">
                <a href="{{ route('issues.show', $issue) }}" class="sp-btn sp-btn-secondary">Batal</a>
                <button type="submit" class="sp-btn sp-btn-primary">
                    Simpan Intervensi &rarr;
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
