<x-app-layout>
    <x-slot name="title">Tambah Data Master</x-slot>

    <div style="max-width:700px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('master-data.index') }}" class="sp-btn sp-btn-ghost sp-btn-sm" style="margin-bottom:0.75rem;">
                &larr; Kembali ke Data Master
            </a>
            <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Tambah Referensi Master</h1>
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

        <form method="POST" action="{{ route('master-data.store') }}" class="sp-card" style="padding:2rem;">
            @csrf

            <div class="sp-form-group">
                <label class="sp-label">Tipe Data Master <span class="required">*</span></label>
                <select name="type" id="type_select" required class="sp-select" onchange="toggleFields(this.value)">
                    <option value="komoditas" {{ old('type') === 'komoditas' ? 'selected' : '' }}>Komoditas Pangan</option>
                    <option value="kabupaten" {{ old('type') === 'kabupaten' ? 'selected' : '' }}>Kabupaten / Kota</option>
                    <option value="pasar" {{ old('type') === 'pasar' ? 'selected' : '' }}>Pasar Tradisional</option>
                </select>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Nama Data <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Beras Premium" required class="sp-input">
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Kode Referensi</label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="Contoh: BRS-01 / SBY" class="sp-input">
                </div>
            </div>

            <div id="komoditas_fields" style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Satuan Ukur</label>
                    <input type="text" name="satuan" value="{{ old('satuan', 'kg') }}" placeholder="kg / liter" class="sp-input">
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Harga Referensi (HET) (Rp)</label>
                    <input type="number" min="0" name="harga_referensi" value="{{ old('harga_referensi') }}" placeholder="14000" class="sp-input">
                </div>
            </div>

            <div class="sp-form-group">
                <label class="sp-label">Deskripsi / Keterangan Wilayah</label>
                <textarea name="deskripsi" rows="2" placeholder="Catatan tambahan atau asosiasi wilayah..." class="sp-textarea">{{ old('deskripsi') }}</textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:1rem;margin-top:2rem;">
                <a href="{{ route('master-data.index') }}" class="sp-btn sp-btn-secondary">Batal</a>
                <button type="submit" class="sp-btn sp-btn-primary">
                    Simpan Master Data &rarr;
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleFields(type) {
            const kf = document.getElementById('komoditas_fields');
            kf.style.display = type === 'komoditas' ? 'grid' : 'none';
        }
        document.addEventListener('DOMContentLoaded', () => {
            toggleFields(document.getElementById('type_select').value);
        });
    </script>
</x-app-layout>
