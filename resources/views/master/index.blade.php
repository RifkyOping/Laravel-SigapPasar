<x-app-layout>
    <x-slot name="title">Kelola Master Data</x-slot>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 style="font-size:1.4rem;font-weight:700;margin:0 0 0.25rem;">Manajemen Data Master</h1>
            <p style="color:var(--clr-ink-2);font-size:0.85rem;margin:0;">Kelola referensi komoditas pangan, harga acuan HET, wilayah kabupaten/kota, dan pasar.</p>
        </div>
        <a href="{{ route('master-data.create') }}" class="sp-btn sp-btn-primary">
            + Tambah Data Baru
        </a>
    </div>

    {{-- Tabs --}}
    <div style="display:flex;gap:0.5rem;margin-bottom:1.5rem;border-bottom:1px solid var(--clr-border);padding-bottom:0.75rem;">
        @foreach(['komoditas' => 'Komoditas Pangan', 'kabupaten' => 'Kabupaten / Kota', 'pasar' => 'Pasar Tradisional'] as $t => $label)
        <a href="{{ route('master-data.index', ['type' => $t]) }}"
           class="sp-btn {{ $type === $t ? 'sp-btn-primary' : 'sp-btn-ghost' }}" style="font-size:0.85rem;padding:0.4rem 1rem;">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="sp-card" style="padding:0;overflow:hidden;">
        <div class="sp-table-wrap" style="border:none;border-radius:0;">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        @if($type === 'komoditas')
                        <th>Satuan</th>
                        <th>Harga Referensi (HET)</th>
                        @else
                        <th>Deskripsi / Wilayah</th>
                        @endif
                        <th>Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td class="cell-code">{{ $item->code ?? '-' }}</td>
                        <td class="cell-primary">{{ $item->name }}</td>
                        @if($type === 'komoditas')
                        <td>{{ $item->satuan ?? '-' }}</td>
                        <td style="color:var(--clr-accent);font-weight:600;">
                            Rp {{ number_format($item->harga_referensi ?? 0, 0, ',', '.') }}
                        </td>
                        @else
                        <td style="color:var(--clr-ink-3);">{{ $item->deskripsi ?? '-' }}</td>
                        @endif
                        <td>
                            <span class="sp-badge {{ $item->is_active ? 'badge-selesai' : 'badge-hoaks' }}">
                                {{ $item->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </td>
                        <td style="text-align:right;white-space:nowrap;">
                            <a href="{{ route('master-data.edit', $item) }}" class="sp-btn sp-btn-ghost sp-btn-sm">
                                Edit
                            </a>
                            @if($item->is_active)
                            <form method="POST" action="{{ route('master-data.destroy', $item) }}" style="display:inline;" onsubmit="return confirm('Nonaktifkan item ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="sp-btn sp-btn-danger sp-btn-sm" style="margin-left:0.3rem;">
                                    Nonaktifkan
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:3rem 0;color:var(--clr-ink-3);">
                            Belum ada data untuk kategori ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid var(--clr-border);">
            {{ $items->appends(['type' => $type])->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
