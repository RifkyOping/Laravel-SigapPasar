<x-app-layout>
    <x-slot name="title">Tugas Lapangan Saya (Tahap 4)</x-slot>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 style="font-size:1.4rem;font-weight:700;margin:0 0 0.25rem;">Tugas Lapangan & Verifikasi Fisik (Tahap 4)</h1>
            <p style="color:var(--clr-ink-2);font-size:0.85rem;margin:0;">
                Daftar inspeksi pasar yang ditugaskan kepada Anda. Lakukan check-in koordinat GPS dan laporkan kondisi harga riil.
            </p>
        </div>
        <span class="sp-badge badge-ditugaskan" style="font-size:0.85rem;padding:0.4rem 0.8rem;">
            {{ $assignments->total() }} Total Tugas
        </span>
    </div>

    <div class="sp-card" style="padding:0;overflow:hidden;">
        <div class="sp-table-wrap" style="border:none;border-radius:0;">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Kode Isu</th>
                        <th>Komoditas & Lokasi Pasar</th>
                        <th>Instruksi Admin</th>
                        <th>Tanggal Tugas</th>
                        <th>Status Tugas</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $asgn)
                    <tr>
                        <td class="cell-code">
                            <a href="{{ route('laporan.show', $asgn) }}" style="color:var(--clr-accent);font-weight:600;">
                                {{ $asgn->issue->kode_isu }}
                            </a>
                        </td>
                        <td>
                            <strong style="color:var(--clr-ink);">{{ $asgn->issue->komoditas->name }}</strong><br>
                            <span style="font-size:0.75rem;color:var(--clr-ink-3);">
                                @php $detailLokasi = $asgn->issue->lokasi_spesifik ?: ($asgn->issue->pasar->name ?? null); @endphp
                                {{ $asgn->issue->kabupaten->name }}{{ $detailLokasi ? ' &bull; ' . $detailLokasi : '' }}
                            </span>
                        </td>
                        <td style="max-width:280px;font-size:0.82rem;color:var(--clr-ink-2);">
                            {{ $asgn->catatan_admin ?? 'Cek fisik harga komoditas dan ketersediaan pasokan di pasar terkait.' }}
                        </td>
                        <td style="font-size:0.8rem;color:var(--clr-ink-3);white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($asgn->tanggal_penugasan)->isoFormat('D MMM YYYY') }}
                        </td>
                        <td>
                            <span class="sp-badge {{ $asgn->status === 'selesai' ? 'badge-selesai' : ($asgn->status === 'sedang_dikerjakan' ? 'badge-di_lapangan' : 'badge-ditugaskan') }}">
                                {{ ucfirst(str_replace('_', ' ', $asgn->status)) }}
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route('laporan.show', $asgn) }}" class="sp-btn sp-btn-primary sp-btn-sm">
                                {{ $asgn->status === 'selesai' ? 'Lihat Laporan' : 'Proses & Lapor' }} &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:3rem 0;color:var(--clr-ink-3);">
                            <div style="font-size:1.1rem;font-weight:600;margin-bottom:0.25rem;">Tidak Ada Tugas Aktif</div>
                            <p style="font-size:0.85rem;margin:0;">Anda saat ini belum memiliki penugasan lapangan yang harus diselesaikan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid var(--clr-border);">
            {{ $assignments->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
