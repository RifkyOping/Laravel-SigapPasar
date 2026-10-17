<x-app-layout>
    <x-slot name="title">Edit Master Data</x-slot>

    <div style="max-width:700px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('master-data.index', ['type' => $item->type]) }}" class="sp-btn sp-btn-ghost sp-btn-sm" style="margin-bottom:0.75rem;">
                &larr; Kembali ke Data Master
            </a>
            <h1 style="font-size:1.5rem;font-weight:700;margin:0;">Edit Master: {{ $item->name }}</h1>
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

        <form method="POST" action="{{ route('master-data.update', $item) }}" class="sp-card" style="padding:2rem;">
            @csrf
            @method('PATCH')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Nama Data <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" required class="sp-input">
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Kode Referensi</label>
                    <input type="text" name="code" value="{{ old('code', $item->code) }}" class="sp-input">
                </div>
            </div>

            @if($item->type === 'komoditas')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div class="sp-form-group">
                    <label class="sp-label">Satuan Ukur</label>
                    <input type="text" name="satuan" value="{{ old('satuan', $item->satuan) }}" class="sp-input">
                </div>

                <div class="sp-form-group">
                    <label class="sp-label">Harga Referensi (HET) (Rp)</label>
                    <input type="number" min="0" name="harga_referensi" value="{{ old('harga_referensi', $item->harga_referensi) }}" class="sp-input">
                </div>
            </div>
            @endif

            <div class="sp-form-group">
                <label class="sp-label">Deskripsi / Keterangan</label>
                <textarea name="deskripsi" rows="2" class="sp-textarea">{{ old('deskripsi', $item->deskripsi) }}</textarea>
            </div>

            <div class="sp-form-group">
                <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;color:var(--clr-ink);cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                    Status Aktif Digunakan
                </label>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:1rem;margin-top:2rem;">
                <a href="{{ route('master-data.index', ['type' => $item->type]) }}" class="sp-btn sp-btn-secondary">Batal</a>
                <button type="submit" class="sp-btn sp-btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
