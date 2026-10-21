<?php

namespace App\Http\Controllers;

use App\Models\CommodityPrice;
use App\Models\Issue;
use App\Models\FieldAssignment;
use App\Models\Intervention;
use App\Models\MasterData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isPimpinan() || $user->isAdmin()) {
            $periode = $request->get('periode', 'hari');
            if (!in_array($periode, ['hari', 'minggu', 'bulan', 'tahun'])) {
                $periode = 'hari';
            }
            $pimpinanData = $this->getPimpinanStats($periode);
            return view('dashboard-pimpinan', array_merge($pimpinanData, compact('user', 'periode')));
        }

        $stats = $this->getStats($user);

        return view('dashboard', compact('stats', 'user'));
    }

    private function getPimpinanStats(string $periode): array
    {
        $now = now();
        switch ($periode) {
            case 'minggu':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $periodeLabel = '7 Hari Terakhir (' . $now->copy()->subDays(6)->format('d M') . ' - ' . $now->format('d M Y') . ')';
                break;
            case 'bulan':
                $startDate = $now->copy()->startOfMonth();
                $periodeLabel = 'Bulan ' . $now->translatedFormat('F Y');
                break;
            case 'tahun':
                $startDate = $now->copy()->startOfYear();
                $periodeLabel = 'Tahun ' . $now->format('Y');
                break;
            case 'hari':
            default:
                $startDate = $now->copy()->startOfDay();
                $periodeLabel = 'Hari Ini (' . $now->translatedFormat('d F Y') . ')';
                break;
        }

        // Isu dalam periode
        $isuQuery = Issue::where('created_at', '>=', $startDate);
        $totalIsu = (clone $isuQuery)->count();
        $isuBaru = (clone $isuQuery)->where('status', 'baru')->count();
        $isuKritis = (clone $isuQuery)->where('prioritas', 'kritis')->count();
        $isuDiproses = (clone $isuQuery)->whereIn('status', ['diverifikasi', 'ditugaskan', 'di_lapangan', 'intervensi'])->count();
        $isuSelesai = (clone $isuQuery)->whereIn('status', ['selesai', 'ditutup'])->count();
        $rasioSelesai = $totalIsu > 0 ? round(($isuSelesai / $totalIsu) * 100) : 0;

        $avgSla = DB::table('resolutions')
            ->where('created_at', '>=', $startDate)
            ->avg('durasi_penanganan_hari')
            ?? DB::table('resolutions')->avg('durasi_penanganan_hari')
            ?? 0;

        // Peta Sebaran 6 Kabupaten di Sulawesi Barat
        $kabupatenCoords = [
            'Kabupaten Mamuju'          => ['lat' => -2.6748, 'lng' => 118.8855, 'code' => 'MMJ'],
            'Kabupaten Majene'          => ['lat' => -3.0245, 'lng' => 118.9489, 'code' => 'MJN'],
            'Kabupaten Polewali Mandar' => ['lat' => -3.4324, 'lng' => 119.3435, 'code' => 'PLM'],
            'Kabupaten Mamasa'          => ['lat' => -2.9333, 'lng' => 119.3833, 'code' => 'MMS'],
            'Kabupaten Pasangkayu'      => ['lat' => -1.1892, 'lng' => 119.3789, 'code' => 'PKG'],
            'Kabupaten Mamuju Tengah'   => ['lat' => -2.0833, 'lng' => 119.2833, 'code' => 'MTG'],
        ];

        $kabupatenList = MasterData::where('type', 'kabupaten')->get();
        $allPasar = MasterData::where('type', 'pasar')->get();
        $petaKabupaten = [];

        foreach ($kabupatenList as $kab) {
            $coords = $kabupatenCoords[$kab->name] ?? ['lat' => -2.8, 'lng' => 119.0, 'code' => $kab->code];

            $isuKab = Issue::where('kabupaten_id', $kab->id);
            $totalKab = (clone $isuKab)->where('created_at', '>=', $startDate)->count();
            $totalKabAll = (clone $isuKab)->count();
            $aktifKab = (clone $isuKab)->whereNotIn('status', ['selesai', 'ditutup', 'hoaks'])->count();
            $kritisKab = (clone $isuKab)->where('prioritas', 'kritis')->whereNotIn('status', ['selesai', 'ditutup'])->count();
            $selesaiKab = (clone $isuKab)->where('status', 'selesai')->count();
            $intervensiKab = (clone $isuKab)->where('status', 'intervensi')->count();
            $lapanganKab = (clone $isuKab)->whereIn('status', ['di_lapangan', 'ditugaskan'])->count();
            $rasioSelesaiKab = $totalKab > 0 ? round(($selesaiKab / $totalKab) * 100) : 100;

            // Komoditas teratas & breakdown di kabupaten ini
            $komoditasBreakdown = (clone $isuKab)
                ->select('komoditas_id', DB::raw('count(*) as count'))
                ->with('komoditas')
                ->groupBy('komoditas_id')
                ->orderByDesc('count')
                ->take(5)
                ->get()
                ->map(fn($item) => [
                    'name' => $item->komoditas?->name ?? 'Lainnya',
                    'count' => $item->count,
                ])
                ->values()
                ->toArray();

            $topKomoditasName = !empty($komoditasBreakdown) ? $komoditasBreakdown[0]['name'] : '-';

            // Pasar di kabupaten ini
            $pasarKab = $allPasar->filter(function($p) use ($kab) {
                return (str_contains($p->deskripsi ?? '', $kab->name)) ||
                       (str_contains($p->name ?? '', $kab->name));
            })->pluck('name')->values()->toArray();

            // Daftar isu terkini di kabupaten ini
            $isuTerkini = (clone $isuKab)
                ->with(['komoditas', 'pasar'])
                ->latest('created_at')
                ->take(6)
                ->get()
                ->map(function ($isu) {
                    return [
                        'id' => $isu->id,
                        'kode_isu' => $isu->kode_isu,
                        'komoditas' => $isu->komoditas?->name ?? '-',
                        'lokasi' => $isu->lokasi_spesifik ?? ($isu->pasar?->name ?? 'Pasar Tradisional'),
                        'status' => $isu->status,
                        'status_label' => $isu->status_label,
                        'prioritas' => $isu->prioritas,
                        'prioritas_label' => $isu->prioritas_label,
                        'virality_score' => (int) $isu->virality_score,
                        'tanggal' => $isu->tanggal_temuan ? $isu->tanggal_temuan->format('d/m/Y') : ($isu->created_at ? $isu->created_at->format('d/m/Y') : '-'),
                        'url' => route('issues.show', $isu->id),
                    ];
                })
                ->values()
                ->toArray();

            // Inspeksi fisik lapangan terbaru di kabupaten ini
            $inspeksiKab = FieldAssignment::whereHas('issue', function($q) use ($kab) {
                    $q->where('kabupaten_id', $kab->id);
                })
                ->with(['issue.komoditas', 'issue.pasar', 'petugas'])
                ->latest()
                ->take(3)
                ->get()
                ->map(function ($asgn) {
                    return [
                        'id' => $asgn->id,
                        'komoditas' => $asgn->issue?->komoditas?->name ?? '-',
                        'lokasi' => $asgn->issue?->lokasi_spesifik ?? ($asgn->issue?->pasar?->name ?? 'Pasar Tradisional'),
                        'harga_aktual' => (float) ($asgn->harga_aktual ?? 0),
                        'petugas' => $asgn->petugas?->name ?? 'Petugas Lapangan',
                        'catatan' => $asgn->catatan_temuan ?? '-',
                        'tanggal' => $asgn->checkin_at ? $asgn->checkin_at->format('d/m/Y H:i') : ($asgn->created_at ? $asgn->created_at->format('d/m/Y') : '-'),
                    ];
                })
                ->values()
                ->toArray();

            $statusLevel = 'kondusif';
            if ($kritisKab > 0) {
                $statusLevel = 'kritis';
            } elseif ($aktifKab > 0) {
                $statusLevel = 'waspada';
            }

            $petaKabupaten[] = [
                'id' => $kab->id,
                'name' => $kab->name,
                'code' => $kab->code,
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'total_isu_periode' => $totalKab,
                'total_isu_all' => $totalKabAll,
                'isu_aktif' => $aktifKab,
                'isu_kritis' => $kritisKab,
                'isu_selesai' => $selesaiKab,
                'isu_intervensi' => $intervensiKab,
                'isu_lapangan' => $lapanganKab,
                'rasio_selesai' => $rasioSelesaiKab,
                'komoditas_utama' => $topKomoditasName,
                'komoditas_breakdown' => $komoditasBreakdown,
                'pasar_list' => $pasarKab,
                'isu_terkini' => $isuTerkini,
                'inspeksi_list' => $inspeksiKab,
                'link_semua_isu' => route('issues.index', ['kabupaten' => $kab->id]),
                'status_level' => $statusLevel,
            ];
        }

        // Titik fisik lapangan (FieldAssignment) yang berkoordinat
        $titikLapangan = FieldAssignment::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['issue.komoditas', 'issue.kabupaten', 'petugas'])
            ->latest()
            ->take(30)
            ->get()
            ->map(function ($asgn) {
                return [
                    'id' => $asgn->id,
                    'lat' => (float) $asgn->latitude,
                    'lng' => (float) $asgn->longitude,
                    'kode_isu' => $asgn->issue?->kode_isu ?? 'ISU',
                    'komoditas' => $asgn->issue?->komoditas?->name ?? '-',
                    'kabupaten' => $asgn->issue?->kabupaten?->name ?? '-',
                    'lokasi' => $asgn->issue?->lokasi_spesifik ?? ($asgn->issue?->pasar?->name ?? 'Pasar Tradisional'),
                    'harga_aktual' => (float) ($asgn->harga_aktual ?? 0),
                    'tanggal' => $asgn->checkin_at ? $asgn->checkin_at->format('d/m/Y H:i') : ($asgn->created_at ? $asgn->created_at->format('d/m/Y') : '-'),
                    'petugas' => $asgn->petugas?->name ?? 'Petugas',
                    'catatan' => $asgn->catatan_temuan ?? '-',
                ];
            });

        // Perkembangan Harga Komoditas Pokok vs HET
        $komoditasList = MasterData::where('type', 'komoditas')
            ->where('is_active', true)
            ->get();

        $totalImportCount = CommodityPrice::count();
        $latestImportDate = CommodityPrice::max('tanggal');

        $perkembanganHarga = [];
        foreach ($komoditasList as $k) {
            $het = (float) $k->harga_referensi;

            // 1. Cek data harga dari commodity_prices (Import Excel)
            $priceQuery = CommodityPrice::where(function($q) use ($k) {
                $q->where('komoditas_id', $k->id)
                  ->orWhere('nama_variant', 'like', '%' . $k->name . '%');
            });

            // Rata-rata atau harga aktual pada periode
            $avgImport = (clone $priceQuery)
                ->where('tanggal', '>=', $startDate->toDateString())
                ->avg('harga');

            // Jika dalam filter periode belum ada, ambil catatan harga paling mutakhir
            if (!$avgImport) {
                $avgImport = (clone $priceQuery)
                    ->latest('tanggal')
                    ->value('harga');
            }

            // 2. Cek harga temuan lapangan (FieldAssignment)
            $avgAktualPeriode = FieldAssignment::whereHas('issue', fn($q) => $q->where('komoditas_id', $k->id))
                ->where('created_at', '>=', $startDate)
                ->whereNotNull('harga_aktual')
                ->where('harga_aktual', '>', 0)
                ->avg('harga_aktual');

            // Fallback ke harga aktual lapangan terakhir
            if (!$avgAktualPeriode) {
                $avgAktualPeriode = FieldAssignment::whereHas('issue', fn($q) => $q->where('komoditas_id', $k->id))
                    ->whereNotNull('harga_aktual')
                    ->where('harga_aktual', '>', 0)
                    ->latest()
                    ->value('harga_aktual');
            }

            // Tentukan harga aktual dan label sumbernya (Prioritas: Inspeksi Lapangan jika lebih tinggi/anomali > Import Pasar > Acuan Master)
            if ($avgAktualPeriode && (!$avgImport || $avgAktualPeriode > $avgImport)) {
                $hargaAktual = (float) $avgAktualPeriode;
                $sumberHarga = 'Inspeksi Lapangan';
                $adaData = true;
            } elseif ($avgImport) {
                $hargaAktual = (float) $avgImport;
                $sumberHarga = 'Import Pasar';
                $adaData = true;
            } elseif ($avgAktualPeriode) {
                $hargaAktual = (float) $avgAktualPeriode;
                $sumberHarga = 'Inspeksi Lapangan';
                $adaData = true;
            } else {
                $hargaAktual = $het;
                $sumberHarga = 'Acuan Master (HET)';
                $adaData = false;
            }

            $selisih = $hargaAktual - $het;
            $persenSelisih = $het > 0 ? round(($selisih / $het) * 100, 1) : 0;

            if ($persenSelisih > 15) {
                $statusHarga = 'Melambung Tinggi';
                $statusClass = 'badge-kritis';
            } elseif ($persenSelisih > 5) {
                $statusHarga = 'Waspada Kenaikan';
                $statusClass = 'badge-intervensi';
            } else {
                $statusHarga = 'Stabil Sesuai HET';
                $statusClass = 'badge-selesai';
            }

            // Hitung isu terkait komoditas ini (prioritaskan isu aktif dan periode berjalan)
            $isuAktifCount = Issue::where('komoditas_id', $k->id)
                ->whereNotIn('status', ['selesai', 'ditutup'])
                ->count();
            $isuPeriodeCount = Issue::where('komoditas_id', $k->id)
                ->where('created_at', '>=', $startDate)
                ->count();
            $isuTotalCount = Issue::where('komoditas_id', $k->id)->count();

            $isuCount = $isuAktifCount > 0 ? $isuAktifCount : ($isuPeriodeCount > 0 ? $isuPeriodeCount : $isuTotalCount);

            $variantsCount = (clone $priceQuery)->distinct('nama_variant')->count('nama_variant');

            $perkembanganHarga[] = [
                'id' => $k->id,
                'name' => $k->name,
                'code' => $k->code,
                'satuan' => $k->satuan ?? 'kg',
                'het' => $het,
                'harga_aktual' => $hargaAktual,
                'sumber_harga' => $sumberHarga,
                'ada_data_lapangan' => $adaData,
                'variants_count' => $variantsCount,
                'selisih' => $selisih,
                'persen_selisih' => $persenSelisih,
                'status_harga' => $statusHarga,
                'status_class' => $statusClass,
                'isu_count' => $isuCount,
                'isu_aktif_count' => $isuAktifCount,
                'isu_total_count' => $isuTotalCount,
            ];
        }

        // Urutkan komoditas:
        // Prioritaskan komoditas yang memiliki isu pasar aktif/terkait, lalu disparitas (persen_selisih) tertinggi
        usort($perkembanganHarga, function ($a, $b) {
            $aHasIsu = ($a['isu_count'] ?? 0) > 0;
            $bHasIsu = ($b['isu_count'] ?? 0) > 0;
            if ($aHasIsu && !$bHasIsu) {
                return -1;
            }
            if (!$aHasIsu && $bHasIsu) {
                return 1;
            }
            return $b['persen_selisih'] <=> $a['persen_selisih'];
        });

        // Data Grafik Tren berdasarkan Periode
        $grafikTren = $this->generateGrafikTren($periode, $startDate, $perkembanganHarga);

        // Statistik ringkas keterbacaan untuk Pimpinan
        $komoditasStabilCount = 0;
        $komoditasWaspadaCount = 0;
        $komoditasKritisCount = 0;

        foreach ($perkembanganHarga as $p) {
            if ($p['persen_selisih'] > 15) {
                $komoditasKritisCount++;
            } elseif ($p['persen_selisih'] > 5) {
                $komoditasWaspadaCount++;
            } else {
                $komoditasStabilCount++;
            }
        }

        $kabupatenKritisCount = count(array_filter($petaKabupaten, fn($kab) => $kab['status_level'] === 'kritis'));
        $kabupatenWaspadaCount = count(array_filter($petaKabupaten, fn($kab) => $kab['status_level'] === 'waspada'));
        $kabupatenAmanCount = count(array_filter($petaKabupaten, fn($kab) => $kab['status_level'] === 'kondusif'));

        if ($komoditasKritisCount > 0) {
            $topProblem = $perkembanganHarga[0]['name'] ?? 'komoditas';
            $topDev = $perkembanganHarga[0]['persen_selisih'] ?? 0;
            $ringkasanEksekutif = "Terdeteksi {$komoditasKritisCount} komoditas melonjak signifikan di atas HET\nTertinggi: {$topProblem} +{$topDev}%";
            $statusDaerahLevel = 'kritis';
            $statusDaerahLabel = 'Perlu Atensi';
        } elseif ($komoditasWaspadaCount > 0) {
            $topProblem = $perkembanganHarga[0]['name'] ?? 'komoditas';
            $topDev = $perkembanganHarga[0]['persen_selisih'] ?? 0;
            $ringkasanEksekutif = "Waspada Kenaikan: {$komoditasWaspadaCount} komoditas mengalami fluktuasi harga di atas HET ({$topProblem} +{$topDev}%). Pasokan komoditas terus dipantau intensif di lapangan.";
            $statusDaerahLevel = 'waspada';
            $statusDaerahLabel = 'Perlu Pemantauan';
        } else {
            $ringkasanEksekutif = "Kondusif & Terkendali: Seluruh komoditas pangan terpantau dalam rentang batas acuan pemerintah (HET). Ketersediaan pasokan wilayah Sulawesi Barat aman.";
            $statusDaerahLevel = 'kondusif';
            $statusDaerahLabel = 'Kondusif & Stabil';
        }

        // Data Isu Terkini Lintas Seluruh Wilayah Sulawesi Barat
        $isuTerkiniSemua = Issue::with(['komoditas', 'kabupaten', 'pasar'])
            ->latest('created_at')
            ->take(10)
            ->get()
            ->map(function ($isu) {
                return [
                    'id' => $isu->id,
                    'kode_isu' => $isu->kode_isu,
                    'komoditas' => $isu->komoditas?->name ?? '-',
                    'lokasi' => ($isu->kabupaten?->name ? $isu->kabupaten->name . ' • ' : '') . ($isu->lokasi_spesifik ?? ($isu->pasar?->name ?? 'Pasar')),
                    'status' => $isu->status,
                    'status_label' => $isu->status_label,
                    'prioritas' => $isu->prioritas,
                    'prioritas_label' => $isu->prioritas_label,
                    'virality_score' => (int) $isu->virality_score,
                    'tanggal' => $isu->tanggal_temuan ? $isu->tanggal_temuan->format('d/m/Y') : ($isu->created_at ? $isu->created_at->format('d/m/Y') : '-'),
                    'url' => route('issues.show', $isu->id),
                ];
            })
            ->values()
            ->toArray();

        $semuaWilayahData = [
            'name' => 'Seluruh Wilayah Sulawesi Barat',
            'code' => 'SULBAR',
            'status_level' => $statusDaerahLevel,
            'status_label' => $statusDaerahLabel,
            'total_isu_periode' => $totalIsu,
            'total_isu_all' => Issue::count(),
            'isu_aktif' => Issue::whereNotIn('status', ['selesai', 'ditutup', 'hoaks'])->count(),
            'isu_kritis' => Issue::where('prioritas', 'kritis')->whereNotIn('status', ['selesai', 'ditutup'])->count(),
            'isu_intervensi' => $isuDiproses,
            'isu_selesai' => $isuSelesai,
            'rasio_selesai' => $rasioSelesai,
            'link_semua_isu' => route('issues.index'),
            'isu_terkini' => $isuTerkiniSemua,
        ];

        return compact(
            'periode', 'periodeLabel', 'startDate',
            'totalIsu', 'isuBaru', 'isuKritis', 'isuDiproses', 'isuSelesai',
            'rasioSelesai', 'avgSla',
            'petaKabupaten', 'titikLapangan', 'semuaWilayahData',
            'perkembanganHarga', 'grafikTren',
            'totalImportCount', 'latestImportDate',
            'komoditasStabilCount', 'komoditasWaspadaCount', 'komoditasKritisCount',
            'kabupatenKritisCount', 'kabupatenWaspadaCount', 'kabupatenAmanCount',
            'ringkasanEksekutif', 'statusDaerahLevel', 'statusDaerahLabel'
        );
    }

    private function generateGrafikTren(string $periode, $startDate, array $perkembanganHarga): array
    {
        $avgHet = (float) (MasterData::where('type', 'komoditas')->where('is_active', true)->avg('harga_referensi') ?: 35000);
        $hasCommodityPrices = CommodityPrice::exists();

        $allKomoditas = $perkembanganHarga;
        foreach ($allKomoditas as &$ak) {
            $ak['chart_prices'] = [];
        }
        unset($ak);

        $labels = [];
        $hargaRataRata = [];
        $hargaHet = [];
        $hargaBeras = [];
        $hargaCabai = [];
        $hargaMinyak = [];
        $latestUploadInfo = null;

        if ($hasCommodityPrices) {
            $latestDate = CommodityPrice::max('tanggal');
            if ($latestDate) {
                $latestUploadInfo = 'Update Terakhir: ' . \Carbon\Carbon::parse($latestDate)->translatedFormat('d M Y');
            }

            if ($periode === 'tahun') {
                $year = now()->year;
                $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                $allYearPrices = CommodityPrice::whereYear('tanggal', $year)
                    ->get(['tanggal', 'komoditas_id', 'nama_variant', 'harga']);

                $monthPriceIndex = [];
                foreach ($allYearPrices as $p) {
                    $m = (int) \Carbon\Carbon::parse($p->tanggal)->format('n');
                    $kId = $p->komoditas_id;
                    $vName = strtolower(trim($p->nama_variant));
                    if ($kId) {
                        $monthPriceIndex[$m]["id_{$kId}"][] = (float) $p->harga;
                    }
                    $monthPriceIndex[$m]["name_{$vName}"][] = (float) $p->harga;
                }

                foreach ($monthNames as $mIdx => $mName) {
                    $m = $mIdx + 1;
                    $labels[] = $mName;
                    $monthAvg = CommodityPrice::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->avg('harga');
                    $hargaRataRata[] = $monthAvg ? round((float) $monthAvg) : null;
                    $hargaHet[] = round($avgHet);
                    $berasAvg = CommodityPrice::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->where('nama_variant', 'like', '%Beras%')->avg('harga');
                    $hargaBeras[] = $berasAvg ? round((float) $berasAvg) : null;
                    $cabaiAvg = CommodityPrice::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->where('nama_variant', 'like', '%Cabai%')->avg('harga');
                    $hargaCabai[] = $cabaiAvg ? round((float) $cabaiAvg) : null;
                    $minyakAvg = CommodityPrice::whereYear('tanggal', $year)->whereMonth('tanggal', $m)->where('nama_variant', 'like', '%Minyak%')->avg('harga');
                    $hargaMinyak[] = $minyakAvg ? round((float) $minyakAvg) : null;

                    foreach ($allKomoditas as &$ak) {
                        $val = null;
                        if (isset($monthPriceIndex[$m]["id_{$ak['id']}"])) {
                            $val = array_sum($monthPriceIndex[$m]["id_{$ak['id']}"]) / count($monthPriceIndex[$m]["id_{$ak['id']}"]);
                        } else {
                            $kNameLower = strtolower(trim($ak['name']));
                            foreach ($monthPriceIndex[$m] ?? [] as $key => $vals) {
                                if (str_starts_with($key, 'name_') && str_contains($key, $kNameLower)) {
                                    $val = array_sum($vals) / count($vals);
                                    break;
                                }
                            }
                        }

                        if (!$val) {
                            $val = $ak['harga_aktual'] ?: $ak['het'];
                        }

                        $ak['chart_prices'][] = round((float) $val);
                    }
                    unset($ak);
                }
            } elseif ($periode === 'bulan') {
                $startOfMonth = now()->startOfMonth()->toDateString();
                $endOfMonth = now()->endOfMonth()->toDateString();
                $datesInMonth = CommodityPrice::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                    ->select('tanggal')
                    ->distinct()
                    ->orderBy('tanggal')
                    ->pluck('tanggal');

                if ($datesInMonth->count() < 3) {
                    $datesInMonth = CommodityPrice::select('tanggal')
                        ->distinct()
                        ->orderBy('tanggal', 'desc')
                        ->take(10)
                        ->pluck('tanggal')
                        ->reverse()
                        ->values();
                }

                $dateStrings = $datesInMonth->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))->toArray();
                $allMonthPrices = CommodityPrice::whereIn(CommodityPrice::raw('DATE(tanggal)'), $dateStrings)
                    ->get(['tanggal', 'komoditas_id', 'nama_variant', 'harga']);

                $datePriceIndex = [];
                foreach ($allMonthPrices as $p) {
                    $dStr = \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d');
                    $kId = $p->komoditas_id;
                    $vName = strtolower(trim($p->nama_variant));
                    if ($kId) {
                        $datePriceIndex[$dStr]["id_{$kId}"][] = (float) $p->harga;
                    }
                    $datePriceIndex[$dStr]["name_{$vName}"][] = (float) $p->harga;
                }

                foreach ($datesInMonth as $d) {
                    $dCarbon = \Carbon\Carbon::parse($d);
                    $dStr = $dCarbon->format('Y-m-d');
                    $labels[] = $dCarbon->translatedFormat('d M');
                    $hargaRataRata[] = round((float) CommodityPrice::whereDate('tanggal', $dStr)->avg('harga'));
                    $hargaHet[] = round($avgHet);
                    $hargaBeras[] = round((float) CommodityPrice::whereDate('tanggal', $dStr)->where('nama_variant', 'like', '%Beras%')->avg('harga'));
                    $hargaCabai[] = round((float) CommodityPrice::whereDate('tanggal', $dStr)->where('nama_variant', 'like', '%Cabai%')->avg('harga'));
                    $hargaMinyak[] = round((float) CommodityPrice::whereDate('tanggal', $dStr)->where('nama_variant', 'like', '%Minyak%')->avg('harga'));

                    foreach ($allKomoditas as &$ak) {
                        $val = null;
                        if (isset($datePriceIndex[$dStr]["id_{$ak['id']}"])) {
                            $val = array_sum($datePriceIndex[$dStr]["id_{$ak['id']}"]) / count($datePriceIndex[$dStr]["id_{$ak['id']}"]);
                        } else {
                            $kNameLower = strtolower(trim($ak['name']));
                            foreach ($datePriceIndex[$dStr] ?? [] as $key => $vals) {
                                if (str_starts_with($key, 'name_') && str_contains($key, $kNameLower)) {
                                    $val = array_sum($vals) / count($vals);
                                    break;
                                }
                            }
                        }

                        if (!$val) {
                            $val = FieldAssignment::whereHas('issue', fn($q) => $q->where('komoditas_id', $ak['id']))
                                ->whereDate('created_at', $dStr)
                                ->whereNotNull('harga_aktual')
                                ->value('harga_aktual');
                        }

                        if (!$val) {
                            $val = $ak['harga_aktual'] ?: $ak['het'];
                        }

                        $ak['chart_prices'][] = round((float) $val);
                    }
                    unset($ak);
                }
            } else {
                // Default 'hari' & 'minggu': ambil tanggal-tanggal upload harian terbaru (sampai 7 hari upload terakhir)
                $distinctDates = CommodityPrice::select('tanggal')
                    ->distinct()
                    ->orderBy('tanggal', 'desc')
                    ->take(7)
                    ->pluck('tanggal')
                    ->reverse()
                    ->values();

                $dateStrings = $distinctDates->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))->toArray();
                $allDayPrices = CommodityPrice::whereIn(CommodityPrice::raw('DATE(tanggal)'), $dateStrings)
                    ->get(['tanggal', 'komoditas_id', 'nama_variant', 'harga']);

                $datePriceIndex = [];
                foreach ($allDayPrices as $p) {
                    $dStr = \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d');
                    $kId = $p->komoditas_id;
                    $vName = strtolower(trim($p->nama_variant));
                    if ($kId) {
                        $datePriceIndex[$dStr]["id_{$kId}"][] = (float) $p->harga;
                    }
                    $datePriceIndex[$dStr]["name_{$vName}"][] = (float) $p->harga;
                }

                foreach ($distinctDates as $d) {
                    $dCarbon = \Carbon\Carbon::parse($d);
                    $dStr = $dCarbon->format('Y-m-d');
                    $labels[] = $dCarbon->translatedFormat('d M');
                    $hargaRataRata[] = round((float) CommodityPrice::whereDate('tanggal', $dStr)->avg('harga'));
                    $hargaHet[] = round($avgHet);
                    $hargaBeras[] = round((float) CommodityPrice::whereDate('tanggal', $dStr)->where('nama_variant', 'like', '%Beras%')->avg('harga'));
                    $hargaCabai[] = round((float) CommodityPrice::whereDate('tanggal', $dStr)->where('nama_variant', 'like', '%Cabai%')->avg('harga'));
                    $hargaMinyak[] = round((float) CommodityPrice::whereDate('tanggal', $dStr)->where('nama_variant', 'like', '%Minyak%')->avg('harga'));

                    foreach ($allKomoditas as &$ak) {
                        $val = null;
                        if (isset($datePriceIndex[$dStr]["id_{$ak['id']}"])) {
                            $val = array_sum($datePriceIndex[$dStr]["id_{$ak['id']}"]) / count($datePriceIndex[$dStr]["id_{$ak['id']}"]);
                        } else {
                            $kNameLower = strtolower(trim($ak['name']));
                            foreach ($datePriceIndex[$dStr] ?? [] as $key => $vals) {
                                if (str_starts_with($key, 'name_') && str_contains($key, $kNameLower)) {
                                    $val = array_sum($vals) / count($vals);
                                    break;
                                }
                            }
                        }

                        if (!$val) {
                            $val = FieldAssignment::whereHas('issue', fn($q) => $q->where('komoditas_id', $ak['id']))
                                ->whereDate('created_at', $dStr)
                                ->whereNotNull('harga_aktual')
                                ->value('harga_aktual');
                        }

                        if (!$val) {
                            $val = $ak['harga_aktual'] ?: $ak['het'];
                        }

                        $ak['chart_prices'][] = round((float) $val);
                    }
                    unset($ak);
                }
            }
        }

        // Fallback jika belum ada data upload harga
        if (empty($labels)) {
            $labels = ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00'];
            $hargaRataRata = array_fill(0, count($labels), round($avgHet));
            $hargaHet = array_fill(0, count($labels), round($avgHet));
            $hargaBeras = array_fill(0, count($labels), 13500);
            $hargaCabai = array_fill(0, count($labels), 60000);
            $hargaMinyak = array_fill(0, count($labels), 20000);

            foreach ($allKomoditas as &$ak) {
                $ak['chart_prices'] = array_fill(0, count($labels), round($ak['harga_aktual'] ?: $ak['het']));
            }
            unset($ak);
        }

        $topKomoditas = array_slice($allKomoditas, 0, 3);

        return [
            'labels' => $labels,
            'harga_rata_rata' => $hargaRataRata,
            'harga_het' => $hargaHet,
            'harga_beras' => $hargaBeras,
            'harga_cabai' => $hargaCabai,
            'harga_minyak' => $hargaMinyak,
            'latest_upload_info' => $latestUploadInfo,
            'top_komoditas' => $topKomoditas,
            'all_komoditas' => $allKomoditas,
            // Backwards compatibility
            'isu_masuk' => array_fill(0, count($labels), 0),
            'isu_selesai' => array_fill(0, count($labels), 0),
        ];
    }

    private function getStats($user): array
    {
        $base = match($user->role) {
            'petugas_lapangan' => Issue::whereHas('assignments', fn($q) => $q->where('petugas_id', $user->id)),
            'pengawas_siber'   => Issue::where('input_by', $user->id),
            default            => Issue::query(),
        };

        $totalIsu        = (clone $base)->count();
        $isuBaru         = (clone $base)->where('status', 'baru')->count();
        $isuKritis       = (clone $base)->where('prioritas', 'kritis')->count();
        $isuSelesai      = (clone $base)->whereIn('status', ['selesai', 'ditutup'])->count();
        $isuIntervensi   = (clone $base)->where('status', 'intervensi')->count();
        $isuDilapangan   = (clone $base)->where('status', 'di_lapangan')->count();

        // SLA stats (avg days to resolution)
        $avgSla = DB::table('resolutions')->avg('durasi_penanganan_hari');

        // Isu per komoditas (top 5)
        $perKomoditas = Issue::select('komoditas_id', DB::raw('count(*) as total'))
            ->with('komoditas')
            ->groupBy('komoditas_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Isu per kabupaten (top 5)
        $perKabupaten = Issue::select('kabupaten_id', DB::raw('count(*) as total'))
            ->with('kabupaten')
            ->groupBy('kabupaten_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Tren bulanan (6 bulan terakhir)
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $yearExpr = $isSqlite ? "strftime('%Y', created_at)" : "YEAR(created_at)";
        $monthExpr = $isSqlite ? "strftime('%m', created_at)" : "MONTH(created_at)";

        $tren = Issue::select(
                DB::raw("{$yearExpr} as tahun"),
                DB::raw("{$monthExpr} as bulan"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        // Isu terbaru
        $isuTerbaru = Issue::with(['komoditas', 'kabupaten', 'inputBy'])
            ->latest()
            ->take(5)
            ->get();

        // Penugasan aktif (untuk petugas)
        $penugasanAktif = $user->isPetugasLapangan()
            ? FieldAssignment::where('petugas_id', $user->id)
                ->where('status', 'ditugaskan')
                ->with(['issue.komoditas', 'issue.kabupaten'])
                ->latest()
                ->take(5)
                ->get()
            : collect();

        return compact(
            'totalIsu', 'isuBaru', 'isuKritis', 'isuSelesai',
            'isuIntervensi', 'isuDilapangan', 'avgSla',
            'perKomoditas', 'perKabupaten', 'tren',
            'isuTerbaru', 'penugasanAktif'
        );
    }
}
