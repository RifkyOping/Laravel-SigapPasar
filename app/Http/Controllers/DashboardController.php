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

        if ($user->isPimpinan()) {
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
        $petaKabupaten = [];

        foreach ($kabupatenList as $kab) {
            $coords = $kabupatenCoords[$kab->name] ?? ['lat' => -2.8, 'lng' => 119.0, 'code' => $kab->code];

            $isuKab = Issue::where('kabupaten_id', $kab->id);
            $totalKab = (clone $isuKab)->where('created_at', '>=', $startDate)->count();
            $aktifKab = (clone $isuKab)->whereNotIn('status', ['selesai', 'ditutup', 'hoaks'])->count();
            $kritisKab = (clone $isuKab)->where('prioritas', 'kritis')->whereNotIn('status', ['selesai', 'ditutup'])->count();

            // Komoditas teratas di kabupaten ini
            $topKomoditas = (clone $isuKab)
                ->select('komoditas_id', DB::raw('count(*) as count'))
                ->with('komoditas')
                ->groupBy('komoditas_id')
                ->orderByDesc('count')
                ->first();

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
                'isu_aktif' => $aktifKab,
                'isu_kritis' => $kritisKab,
                'komoditas_utama' => $topKomoditas?->komoditas?->name ?? 'Nihil Isu',
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

            // Tentukan harga aktual dan label sumbernya (Prioritas: Import Pasar > Inspeksi Lapangan > Acuan Master)
            if ($avgImport) {
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

            $isuCount = Issue::where('komoditas_id', $k->id)
                ->where('created_at', '>=', $startDate)
                ->count();

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
            ];
        }

        // Urutkan komoditas dari kenaikan tertinggi
        usort($perkembanganHarga, fn($a, $b) => $b['persen_selisih'] <=> $a['persen_selisih']);

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
            $ringkasanEksekutif = "Perhatian Khusus: Terdeteksi {$komoditasKritisCount} komoditas melonjak signifikan di atas HET (tertinggi: {$topProblem} +{$topDev}%). Disarankan pertimbangan koordinasi operasi pasar.";
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

        return compact(
            'periode', 'periodeLabel', 'startDate',
            'totalIsu', 'isuBaru', 'isuKritis', 'isuDiproses', 'isuSelesai',
            'rasioSelesai', 'avgSla',
            'petaKabupaten', 'titikLapangan',
            'perkembanganHarga', 'grafikTren',
            'totalImportCount', 'latestImportDate',
            'komoditasStabilCount', 'komoditasWaspadaCount', 'komoditasKritisCount',
            'kabupatenKritisCount', 'kabupatenWaspadaCount', 'kabupatenAmanCount',
            'ringkasanEksekutif', 'statusDaerahLevel', 'statusDaerahLabel'
        );
    }

    private function generateGrafikTren(string $periode, $startDate, array $perkembanganHarga): array
    {
        $labels = [];
        $isuMasukData = [];
        $isuSelesaiData = [];

        if ($periode === 'hari') {
            $labels = ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00'];
            $now = now();
            foreach ($labels as $idx => $lbl) {
                $hour = 8 + ($idx * 2);
                $slotStart = $now->copy()->setTime($hour, 0, 0);
                $slotEnd = $now->copy()->setTime($hour + 2, 0, 0);
                $isuMasukData[] = Issue::whereBetween('created_at', [$slotStart, $slotEnd])->count();
                $isuSelesaiData[] = Issue::whereBetween('updated_at', [$slotStart, $slotEnd])
                    ->whereIn('status', ['selesai', 'ditutup'])->count();
            }
        } elseif ($periode === 'minggu') {
            for ($i = 6; $i >= 0; $i--) {
                $day = now()->subDays($i);
                $labels[] = $day->translatedFormat('D (d M)');
                $start = $day->copy()->startOfDay();
                $end = $day->copy()->endOfDay();
                $isuMasukData[] = Issue::whereBetween('created_at', [$start, $end])->count();
                $isuSelesaiData[] = Issue::whereBetween('updated_at', [$start, $end])
                    ->whereIn('status', ['selesai', 'ditutup'])->count();
            }
        } elseif ($periode === 'tahun') {
            $year = now()->year;
            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            foreach ($monthNames as $mIdx => $mName) {
                $labels[] = $mName;
                $m = $mIdx + 1;
                $isuMasukData[] = Issue::whereYear('created_at', $year)->whereMonth('created_at', $m)->count();
                $isuSelesaiData[] = Issue::whereYear('updated_at', $year)->whereMonth('updated_at', $m)
                    ->whereIn('status', ['selesai', 'ditutup'])->count();
            }
        } else { // bulan
            $startOfMonth = now()->startOfMonth();
            $labels = ['Minggu 1 (Tgl 1-7)', 'Minggu 2 (Tgl 8-14)', 'Minggu 3 (Tgl 15-21)', 'Minggu 4 (Tgl 22+)'];
            $ranges = [
                [$startOfMonth->copy()->day(1)->startOfDay(), $startOfMonth->copy()->day(7)->endOfDay()],
                [$startOfMonth->copy()->day(8)->startOfDay(), $startOfMonth->copy()->day(14)->endOfDay()],
                [$startOfMonth->copy()->day(15)->startOfDay(), $startOfMonth->copy()->day(21)->endOfDay()],
                [$startOfMonth->copy()->day(22)->startOfDay(), now()->endOfMonth()->endOfDay()],
            ];
            foreach ($ranges as $rng) {
                $isuMasukData[] = Issue::whereBetween('created_at', [$rng[0], $rng[1]])->count();
                $isuSelesaiData[] = Issue::whereBetween('updated_at', [$rng[0], $rng[1]])
                    ->whereIn('status', ['selesai', 'ditutup'])->count();
            }
        }

        // Ambil top 3 komoditas untuk perbandingan harga visual
        $top3 = array_slice($perkembanganHarga, 0, 3);

        return [
            'labels' => $labels,
            'isu_masuk' => $isuMasukData,
            'isu_selesai' => $isuSelesaiData,
            'top_komoditas' => $top3,
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
