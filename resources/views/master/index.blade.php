<x-app-layout>
    <x-slot name="title">Kelola Master Data</x-slot>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 style="font-size:1.4rem;font-weight:700;margin:0 0 0.25rem;">Manajemen Data Master</h1>
            <p style="color:var(--clr-ink-2);font-size:0.85rem;margin:0;">Kelola referensi komoditas pangan, harga acuan HET/HAP, wilayah kabupaten/kota, dan pasar.</p>
        </div>
        <a href="{{ route('master-data.create') }}" class="sp-btn sp-btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Data Baru
        </a>
    </div>

    {{-- Tabs --}}
    <div style="display:flex;gap:0.5rem;margin-bottom:1.25rem;border-bottom:1px solid var(--clr-border);padding-bottom:0.75rem;flex-wrap:wrap;">
        @foreach(['komoditas' => 'Komoditas Pangan', 'kabupaten' => 'Kabupaten / Kota', 'pasar' => 'Pasar Tradisional'] as $t => $label)
        <a href="{{ route('master-data.index', array_filter(['type' => $t, 'search' => $search ?? null])) }}"
           class="sp-btn {{ $type === $t ? 'sp-btn-primary' : 'sp-btn-ghost' }}" style="font-size:0.85rem;padding:0.4rem 1rem;">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Filter Kata Kunci --}}
    <div class="sp-card" style="margin-bottom:1.5rem;padding:1rem 1.25rem;">
        <form method="GET" action="{{ route('master-data.index') }}" style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
            <input type="hidden" name="type" value="{{ $type }}">

            <div style="position:relative;flex:1;min-width:240px;">
                <span style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:var(--clr-ink-3);display:flex;align-items:center;pointer-events:none;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari kata kunci {{ $type === 'komoditas' ? 'komoditas' : ($type === 'kabupaten' ? 'kabupaten' : 'pasar') }}, kode..." class="sp-input" style="padding:0.5rem 0.75rem 0.5rem 2.25rem;font-size:0.85rem;width:100%;">
            </div>

            <button type="submit" class="sp-btn sp-btn-primary" style="padding:0.5rem 1.2rem;font-size:0.85rem;height:38px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Cari
            </button>

            @if(!empty($search))
            <a href="{{ route('master-data.index', ['type' => $type]) }}" class="sp-btn sp-btn-ghost" style="padding:0.5rem 1rem;font-size:0.85rem;height:38px;border:1px solid var(--clr-border);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                Reset
            </a>
            @endif
        </form>
    </div>

    <div class="sp-card" style="padding:0;overflow:hidden;">
        <div class="sp-table-wrap" style="border:none;border-radius:0;">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        @if($type === 'komoditas')
                        <th>Komoditas</th>
                        <th>Satuan</th>
                        <th>HET/HAP</th>
                        @elseif($type === 'all')
                        <th>Komoditas / Nama</th>
                        <th>Tipe</th>
                        <th>Satuan</th>
                        <th>HET/HAP</th>
                        @else
                        <th>Nama</th>
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
                        @elseif($type === 'all')
                        <td>
                            <span class="sp-badge badge-baru" style="text-transform:capitalize;">{{ $item->type }}</span>
                        </td>
                        <td>{{ $item->satuan ?? '-' }}</td>
                        <td style="color:var(--clr-accent);font-weight:600;">
                            {{ $item->harga_referensi ? 'Rp ' . number_format($item->harga_referensi, 0, ',', '.') : '-' }}
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
                            @if(!empty($search))
                                Tidak ada data master yang sesuai dengan kata kunci pencarian.
                                <div style="margin-top:0.5rem;">
                                    <a href="{{ route('master-data.index', ['type' => $type]) }}" class="sp-btn sp-btn-ghost sp-btn-sm">
                                        Reset Pencarian
                                    </a>
                                </div>
                            @else
                                Belum ada data untuk kategori ini.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid var(--clr-border);">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
