<x-app-layout>
    <x-slot name="title">Data Harga Harian Pasar & Import Excel</x-slot>

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.35rem;">
                <span class="sp-badge badge-selesai">Stabilisasi Pangan</span>
                <span style="font-size:0.85rem;color:var(--clr-ink-3);">SP2KP & Pemantauan Pasar Daerah</span>
            </div>
            <h1 style="font-size:1.6rem;font-weight:700;margin:0 0 0.35rem;letter-spacing:-0.03em;color:var(--clr-ink);">
                {{ auth()->user()->isPimpinan() ? 'Data Pantauan Harga Pasar Harian' : 'Data Harga Harian Komoditas & Import Excel' }}
            </h1>
            <p style="color:var(--clr-ink-2);font-size:0.9rem;margin:0;">
                {{ auth()->user()->isPimpinan() ? 'Rincian perkembangan harga harian komoditas pangan pokok per varian di pasar-pasar Sulawesi Barat.' : 'Kelola data perkembangan harga pasar harian di Sulawesi Barat. Unggah file spreadsheet (Excel / CSV) untuk memperbarui harga berkala.' }}
            </p>
        </div>

        @if(auth()->user()->isRole(['admin', 'super_admin']))
        <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
            <a href="{{ route('prices.template') }}" class="sp-btn sp-btn-secondary" title="Unduh format spreadsheet siap pakai">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Unduh Format Excel
            </a>
            <button type="button" onclick="document.getElementById('import-card').scrollIntoView({behavior: 'smooth'})" class="sp-btn sp-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Import Excel Harga
            </button>
        </div>
        @endif
    </div>

    {{-- KPI Cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:1rem;margin-bottom:2rem;">
        <div class="sp-stat-card" style="border-left:3px solid var(--clr-accent);">
            <span class="sp-stat-label">Total Catatan Harga</span>
            <div class="sp-stat-value" style="color:var(--clr-accent);">{{ number_format($totalRecords) }}</div>
            <span class="sp-stat-sub">Harga per varian per tanggal</span>
        </div>

        <div class="sp-stat-card" style="border-left:3px solid var(--clr-baru);">
            <span class="sp-stat-label">Jumlah Varian Terpantau</span>
            <div class="sp-stat-value" style="color:var(--clr-baru);">{{ $totalVariants }}</div>
            <span class="sp-stat-sub">Bawang, beras, cabai, daging, dsb.</span>
        </div>

        <div class="sp-stat-card" style="border-left:3px solid var(--clr-selesai);">
            <span class="sp-stat-label">Pembaruan Tanggal Terakhir</span>
            <div class="sp-stat-value" style="color:var(--clr-selesai);font-size:1.4rem;">
                {{ $latestDate ? \Carbon\Carbon::parse($latestDate)->isoFormat('D MMMM YYYY') : 'Belum Ada' }}
            </div>
            <span class="sp-stat-sub">Tanggal laporan mutakhir</span>
        </div>
    </div>

    @if(auth()->user()->isRole(['admin', 'super_admin']))
    {{-- Form Import Excel Card --}}
    <div class="sp-card sp-import-card" id="import-card">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <h2 style="font-size:1.15rem;font-weight:700;margin:0 0 0.25rem;">
                    Unggah File Spreadsheet Harga Harian
                </h2>
                <p style="font-size:0.83rem;margin:0;" class="sp-import-subtitle">
                    Mendukung file berekstensi <strong>.xlsx</strong>, <strong>.xls</strong>, atau <strong>.csv</strong>. Sistem akan membaca kolom tanggal (mulai kolom C) dan otomatis memperbarui data jika tanggal dan varian sudah ada.
                </p>
            </div>
            <a href="{{ route('prices.template') }}" class="sp-btn sp-btn-ghost sp-btn-sm sp-import-template-link">
                Lihat Contoh Format &rarr;
            </a>
        </div>

        <form action="{{ route('prices.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:flex-end;">
                <div>
                    <label class="sp-label" for="file_excel">
                        Pilih File Excel / CSV <span class="required">*</span>
                    </label>
                    <input type="file" 
                           id="file_excel" 
                           name="file_excel" 
                           accept=".xlsx,.xls,.csv" 
                           required 
                           class="sp-input"
                           style="padding:8px 12px;cursor:pointer;">
                </div>
                <div>
                    <button type="submit" class="sp-btn sp-btn-primary" style="height:44px;padding:0 1.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/></svg>
                        Mulai Import Data
                    </button>
                </div>
            </div>

            <div class="sp-import-hint">
                <strong class="hint-title">Petunjuk Struktur Kolom File Excel:</strong><br>
                &bull; <strong>Kolom A:</strong> Nama Variant (contoh: <code>Bawang Merah</code>, <code>Beras Premium</code>, <code>Cabai Rawit Merah</code>, <code>Gas LPG 3kg</code>, <code>BBM</code>)<br>
                &bull; <strong>Kolom B:</strong> Satuan (contoh: <code>Rp/Kg</code>, <code>Rp/Liter</code>, <code>Rp/Tabung</code>)<br>
                &bull; <strong>Kolom C, D, E, dst:</strong> Judul kolom berupa Tanggal (contoh: <code>12/10/26</code>, <code>13/10/26</code>, atau <code>12/10/2026</code>) dengan nilai angka harga rupiah di setiap baris.
            </div>
        </form>
    </div>
    @endif

    {{-- Filter & Data Table Card --}}
    <div class="sp-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <h2 style="font-size:1.1rem;font-weight:700;margin:0 0 0.2rem;color:var(--clr-ink);">
                    Daftar Harga Tercatat
                </h2>
                <p style="color:var(--clr-ink-3);font-size:0.8rem;margin:0;">
                    Menampilkan seluruh pencatatan harga hasil import dan survei lapangan.
                </p>
            </div>

            {{-- Filter Form --}}
            <form action="{{ route('prices.index') }}" method="GET" style="display:flex;gap:0.6rem;flex-wrap:wrap;align-items:center;">
                <select name="tanggal" class="sp-select" style="min-width:160px;font-size:0.85rem;" onchange="this.form.submit()">
                    <option value="">-- Semua Tanggal --</option>
                    @foreach($availableDates as $ad)
                        <option value="{{ $ad->tanggal->format('Y-m-d') }}" {{ request('tanggal') === $ad->tanggal->format('Y-m-d') ? 'selected' : '' }}>
                            {{ $ad->tanggal->isoFormat('D MMM YYYY') }} ({{ $ad->count }})
                        </option>
                    @endforeach
                </select>

                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari varian..." 
                       class="sp-input" 
                       style="width:180px;font-size:0.85rem;">

                <button type="submit" class="sp-btn sp-btn-secondary sp-btn-sm">Filter</button>
                @if(request()->filled('tanggal') || request()->filled('search'))
                    <a href="{{ route('prices.index') }}" class="sp-btn sp-btn-ghost sp-btn-sm">Reset</a>
                @endif
            </form>
        </div>

        <div class="sp-table-wrap">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Variant</th>
                        <th>Satuan</th>
                        <th style="text-align:right;">Harga Tercatat (Rp)</th>
                        <th>Master Komoditas</th>
                        <th>Sumber</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prices as $item)
                    <tr>
                        <td style="font-weight:600;color:var(--clr-ink-2);white-space:nowrap;">
                            {{ $item->tanggal->isoFormat('dddd, D MMMM YYYY') }}
                        </td>
                        <td class="cell-primary">
                            <strong style="color:var(--clr-ink);">{{ $item->nama_variant }}</strong>
                        </td>
                        <td>
                            <span class="sp-badge badge-ditugaskan" style="font-size:0.75rem;">
                                {{ $item->satuan }}
                            </span>
                        </td>
                        <td style="text-align:right;font-weight:700;color:#38bdf8;font-size:0.95rem;">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($item->komoditas)
                                <span style="color:var(--clr-accent-3);font-weight:600;font-size:0.85rem;">
                                    {{ $item->komoditas->name }}
                                </span>
                            @else
                                <span style="color:var(--clr-ink-3);font-size:0.8rem;">Varian Khusus</span>
                            @endif
                        </td>
                        <td>
                            <span class="sp-badge badge-selesai" style="font-size:0.72rem;">
                                {{ $item->sumber_label }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:3rem 0;color:var(--clr-ink-3);">
                            Belum ada data harga yang diimpor. Silakan klik tombol <strong>"Unduh Format Excel"</strong> lalu unggah kembali melalui form di atas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($prices->hasPages())
        <div style="margin-top:1.25rem;">
            {{ $prices->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
