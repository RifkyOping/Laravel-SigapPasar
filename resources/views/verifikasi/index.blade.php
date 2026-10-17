<x-app-layout>
    <x-slot name="title">Antrian Verifikasi Laporan</x-slot>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 style="font-size:1.4rem;font-weight:700;margin:0 0 0.25rem;">Antrian Verifikasi Isu Siber (Tahap 2)</h1>
            <p style="color:var(--clr-ink-2);font-size:0.85rem;margin:0;">
                Saring keabsahan laporan siber, tentukan tingkat prioritas, dan periksa potensi duplikasi atau hoaks.
            </p>
        </div>
        <span class="sp-badge badge-baru" style="font-size:0.85rem;padding:0.4rem 0.8rem;">
            {{ $issues->total() }} Menunggu Verifikasi
        </span>
    </div>

    <div class="sp-card" style="padding:0;overflow:hidden;">
        <div class="sp-table-wrap" style="border:none;border-radius:0;">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Kode Isu</th>
                        <th>Komoditas & Wilayah</th>
                        <th>Kutipan Postingan</th>
                        <th>Platform / Akun</th>
                        <th>Virality</th>
                        <th>Waktu Temuan</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($issues as $isu)
                    <tr>
                        <td class="cell-code">
                            <a href="{{ route('verifikasi.show', $isu) }}" style="color:var(--clr-accent);font-weight:600;">
                                {{ $isu->kode_isu }}
                            </a>
                        </td>
                        <td>
                            <strong style="color:var(--clr-ink);">{{ $isu->komoditas->name ?? '-' }}</strong><br>
                            <span style="font-size:0.75rem;color:var(--clr-ink-3);">{{ $isu->kabupaten->name ?? '-' }}</span>
                        </td>
                        <td style="max-width:320px;">
                            <div style="font-size:0.85rem;color:var(--clr-ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                "{{ $isu->kutipan_postingan }}"
                            </div>
                            <span style="font-size:0.75rem;color:var(--clr-accent);">
                                Kategori: {{ ucfirst(str_replace('_', ' ', $isu->kategori_isu)) }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size:0.85rem;color:var(--clr-ink);font-weight:500;">{{ $isu->platform }}</div>
                            <small style="color:var(--clr-ink-3);">{{ $isu->nama_akun ?? 'Anonim' }}</small>
                        </td>
                        <td>
                            <span style="font-weight:700;color:var(--clr-ink);">{{ number_format($isu->virality_score) }}</span>
                            <div style="font-size:0.7rem;color:var(--clr-ink-3);">{{ number_format($isu->jumlah_interaksi) }} interaksi</div>
                        </td>
                        <td style="font-size:0.78rem;color:var(--clr-ink-3);white-space:nowrap;">
                            {{ $isu->tanggal_temuan->diffForHumans() }}
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('verifikasi.show', $isu) }}" class="sp-btn sp-btn-primary sp-btn-sm">
                                Proses Verifikasi &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:3rem 0;color:var(--clr-ink-3);">
                            <div style="font-size:1.1rem;font-weight:600;margin-bottom:0.25rem;">Tidak Ada Antrian Verifikasi</div>
                            <p style="font-size:0.85rem;margin:0;">Semua laporan siber yang masuk telah berhasil diverifikasi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($issues->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid var(--clr-border);">
            {{ $issues->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
