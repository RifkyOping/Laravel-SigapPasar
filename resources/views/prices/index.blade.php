<x-app-layout>
    <x-slot name="title">Data Pantauan Harga Pasar Harian</x-slot>

    <style>
        .sp-table-header-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .sp-filter-form-inline {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin: 0;
        }
        .sp-filter-form-inline .sp-filter-item {
            position: relative;
            display: flex;
            align-items: center;
        }
        .sp-filter-form-inline .sp-filter-select {
            width: auto !important;
            min-width: 175px;
            height: 38px;
            padding: 0.35rem 2.2rem 0.35rem 0.85rem;
            font-size: 0.85rem;
            line-height: 1.2;
            border-radius: 6px;
        }
        .sp-filter-form-inline .sp-filter-select-perpage {
            min-width: 140px;
        }
        .sp-filter-form-inline .sp-filter-search-box {
            position: relative;
        }
        .sp-filter-form-inline .sp-search-icon {
            position: absolute;
            left: 0.75rem;
            width: 14px;
            height: 14px;
            color: var(--clr-ink-3);
            pointer-events: none;
        }
        .sp-filter-form-inline .sp-filter-input {
            width: 180px !important;
            height: 38px;
            padding: 0.35rem 0.75rem 0.35rem 2.2rem;
            font-size: 0.85rem;
            line-height: 1.2;
            border-radius: 6px;
        }
        .sp-filter-form-inline .sp-filter-actions {
            display: flex;
            gap: 0.4rem;
            align-items: center;
        }
        .sp-filter-form-inline .sp-filter-btn {
            height: 38px;
            padding: 0 0.9rem;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            white-space: nowrap;
            border-radius: 6px;
        }
        @media (max-width: 1024px) {
            .sp-table-header-toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            .sp-filter-form-inline {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .sp-filter-form-inline .sp-filter-item {
                flex: 1 1 160px;
            }
            .sp-filter-form-inline .sp-filter-select,
            .sp-filter-form-inline .sp-filter-input {
                width: 100% !important;
                min-width: 0;
            }
            .sp-filter-form-inline .sp-filter-actions {
                flex: 0 0 auto;
            }
        }
        @media (max-width: 640px) {
            .sp-filter-form-inline .sp-filter-item {
                flex: 1 1 100%;
            }
            .sp-filter-form-inline .sp-filter-actions {
                width: 100%;
                display: grid;
                grid-template-columns: 1fr 1fr;
            }
            .sp-filter-form-inline .sp-filter-btn {
                justify-content: center;
            }
        }
    </style>


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
                <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
                    <button type="submit" class="sp-btn sp-btn-primary" style="height:44px;padding:0 1.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/></svg>
                        Mulai Import Data
                    </button>
                    <a href="{{ route('prices.template') }}" class="sp-btn sp-btn-secondary" style="height:44px;padding:0 1.25rem;display:inline-flex;align-items:center;gap:0.5rem;" title="Unduh format spreadsheet siap pakai">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Unduh Format Excel
                    </a>
                </div>
            </div>

        </form>
    </div>
    @endif

    {{-- Filter & Data Table Card --}}
    <div class="sp-card">
        <div class="sp-table-header-toolbar">
            <div>
                <h2 style="font-size:1.1rem;font-weight:700;margin:0 0 0.25rem;color:var(--clr-ink);display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                    Daftar Harga Tercatat
                    <span class="sp-badge badge-ditugaskan" style="font-size:0.72rem;padding:0.15rem 0.5rem;font-weight:600;">
                        {{ number_format($prices->total()) }} Data
                    </span>
                </h2>
            </div>

            {{-- Filter Form --}}
            <form action="{{ route('prices.index') }}" method="GET" class="sp-filter-form-inline">
                {{-- Dropdown Kabupaten --}}
                <div class="sp-filter-item">
                    <select name="kabupaten" class="sp-select sp-filter-select" onchange="this.form.submit()" title="Filter berdasarkan kabupaten">
                        <option value="">-- Semua Kabupaten --</option>
                        @foreach($availableKabupaten as $kab)
                            <option value="{{ $kab->id }}" {{ request('kabupaten') == $kab->id ? 'selected' : '' }}>
                                {{ str_replace('Kabupaten ', '', $kab->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Dropdown Tanggal --}}
                <div class="sp-filter-item">
                    <select name="tanggal" class="sp-select sp-filter-select" onchange="this.form.submit()" title="Filter berdasarkan tanggal pencatatan">
                        <option value="">-- Semua Tanggal --</option>
                        @foreach($availableDates as $ad)
                            <option value="{{ $ad->tanggal->format('Y-m-d') }}" {{ request('tanggal') === $ad->tanggal->format('Y-m-d') ? 'selected' : '' }}>
                                {{ $ad->tanggal->isoFormat('D MMM YYYY') }} ({{ $ad->count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Dropdown / Input Jumlah Data Tampil --}}
                <div class="sp-filter-item" id="per-page-container">
                    <select name="per_page" id="per_page_select" class="sp-select sp-filter-select sp-filter-select-perpage" onchange="handlePerPageChange(this)" title="Pilih jumlah data yang ditampilkan">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 Data</option>
                        <option value="25" {{ (request('per_page', 25) == '25' && request('per_page') !== 'all') ? 'selected' : '' }}>25 Data</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 Data</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 Data</option>
                        <option value="all" {{ request('per_page') === 'all' ? 'selected' : '' }}>Tampilkan Semua ({{ number_format($totalRecords) }})</option>
                        @if(request()->filled('per_page') && !in_array(request('per_page'), ['10', '25', '50', '100', 'all']))
                            <option value="{{ (int)request('per_page') }}" selected>{{ (int)request('per_page') }} data (Kustom)</option>
                        @endif
                        <option value="custom">&#9881; Masukkan Angka</option>
                    </select>

                    <div id="custom-per-page-box" style="display:none;align-items:center;gap:0.3rem;">
                        <input type="number" 
                               id="custom_per_page_input" 
                               min="1" 
                               max="2000" 
                               placeholder="Jml" 
                               class="sp-input" 
                               style="width:80px;height:38px;padding:0.35rem 0.5rem;text-align:center;font-size:0.85rem;"
                               value="{{ !in_array(request('per_page'), ['10', '25', '50', '100', 'all', null]) ? request('per_page') : '' }}">
                        <button type="button" onclick="applyCustomPerPage()" class="sp-btn sp-btn-secondary" style="height:38px;padding:0 0.65rem;font-size:0.8rem;" title="Terapkan">
                            OK
                        </button>
                        <button type="button" onclick="cancelCustomPerPage()" class="sp-btn sp-btn-ghost" style="height:38px;padding:0 0.45rem;font-size:0.8rem;" title="Batal">
                            ✕
                        </button>
                    </div>
                </div>

                {{-- Input Cari Varian dengan Ikon --}}
                <div class="sp-filter-item sp-filter-search-box">
                    <svg class="sp-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari komoditas/wilayah..." 
                           class="sp-input sp-filter-input">
                </div>

                {{-- Action Buttons --}}
                <div class="sp-filter-actions">
                    <button type="submit" class="sp-btn sp-btn-secondary sp-filter-btn" title="Terapkan filter pencarian">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Filter
                    </button>
                    @if(request()->filled('tanggal') || request()->filled('kabupaten') || request()->filled('search') || (request()->filled('per_page') && request('per_page') != '25'))
                        <a href="{{ route('prices.index') }}" class="sp-btn sp-btn-ghost sp-filter-btn" title="Reset semua filter">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="sp-table-wrap">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Kabupaten</th>
                        <th>Komoditas</th>
                        <th>Satuan</th>
                        <th>Tanggal</th>
                        <th style="text-align:right;">Harga (Rp)</th>
                        <th>Master Komoditas</th>
                        <th>Sumber</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prices as $item)
                    <tr>
                        <td>
                            <span class="sp-badge badge-ditugaskan" style="font-weight:600;font-size:0.8rem;background:rgba(56, 189, 248, 0.12);color:#38bdf8;border:1px solid rgba(56, 189, 248, 0.25);">
                                {{ $item->kabupaten_label }}
                            </span>
                        </td>
                        <td class="cell-primary">
                            <strong style="color:var(--clr-ink);">{{ $item->nama_variant }}</strong>
                        </td>
                        <td>
                            <span class="sp-badge badge-ditugaskan" style="font-size:0.75rem;">
                                {{ $item->satuan }}
                            </span>
                        </td>
                        <td style="font-weight:600;color:var(--clr-ink-2);white-space:nowrap;">
                            {{ $item->tanggal ? $item->tanggal->isoFormat('D MMM YYYY') : '-' }}
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
                        <td colspan="7" style="text-align:center;padding:3rem 0;color:var(--clr-ink-3);">
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
        @elseif($prices->total() > 0)
        <div style="margin-top:1.25rem;display:flex;justify-content:space-between;align-items:center;font-size:0.82rem;color:var(--clr-ink-3);padding-top:0.75rem;border-top:1px solid var(--clr-border);flex-wrap:wrap;gap:0.5rem;">
            <span>Menampilkan seluruh <strong>{{ number_format($prices->total()) }}</strong> data harga tercatat.</span>
            @if(request('per_page') === 'all')
                <span class="sp-badge badge-selesai" style="font-size:0.75rem;">Mode Semua Data Aktif</span>
            @endif
        </div>
        @endif
    </div>

    <script>
        function handlePerPageChange(select) {
            if (select.value === 'custom') {
                select.style.display = 'none';
                const box = document.getElementById('custom-per-page-box');
                const input = document.getElementById('custom_per_page_input');
                box.style.display = 'flex';
                input.focus();
                input.select();
            } else {
                select.form.submit();
            }
        }

        function applyCustomPerPage() {
            const input = document.getElementById('custom_per_page_input');
            const val = parseInt(input.value);
            const select = document.getElementById('per_page_select');
            if (val && !isNaN(val) && val > 0) {
                let opt = select.querySelector('option[data-custom="true"]');
                if (!opt) {
                    opt = document.createElement('option');
                    opt.setAttribute('data-custom', 'true');
                    select.appendChild(opt);
                }
                opt.value = val;
                opt.textContent = val + ' data (Kustom)';
                opt.selected = true;
                select.form.submit();
            } else {
                cancelCustomPerPage();
            }
        }

        function cancelCustomPerPage() {
            const select = document.getElementById('per_page_select');
            const box = document.getElementById('custom-per-page-box');
            box.style.display = 'none';
            select.style.display = '';
            select.value = '{{ request('per_page', 25) }}';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const customInput = document.getElementById('custom_per_page_input');
            if (customInput) {
                customInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyCustomPerPage();
                    } else if (e.key === 'Escape') {
                        cancelCustomPerPage();
                    }
                });
            }
        });
    </script>
</x-app-layout>
