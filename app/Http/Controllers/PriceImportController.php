<?php

namespace App\Http\Controllers;

use App\Models\CommodityPrice;
use App\Models\MasterData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PriceImportController extends Controller
{
    public function index(Request $request)
    {
        $query = CommodityPrice::with(['komoditas', 'kabupaten'])->latest('tanggal');

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        }

        if ($request->filled('kabupaten')) {
            $kabVal = $request->kabupaten;
            $query->where(function ($q) use ($kabVal) {
                $q->where('kabupaten_id', $kabVal)
                  ->orWhere('nama_kabupaten', $kabVal)
                  ->orWhereHas('kabupaten', fn($k) => $k->where('name', 'like', "%{$kabVal}%"));
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_variant', 'like', "%{$search}%")
                  ->orWhere('nama_kabupaten', 'like', "%{$search}%")
                  ->orWhereHas('kabupaten', fn($k) => $k->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('komoditas', fn($k) => $k->where('name', 'like', "%{$search}%"));
            });
        }

        $perPage = $request->input('per_page', 25);
        if ($perPage === 'all' || $perPage === '-1') {
            $totalCount = (clone $query)->count();
            $prices = $query->paginate(max($totalCount, 1))->withQueryString();
        } elseif (is_numeric($perPage) && (int)$perPage > 0) {
            $perPage = min(max((int)$perPage, 1), 2000);
            $prices = $query->paginate($perPage)->withQueryString();
        } else {
            $prices = $query->paginate(25)->withQueryString();
        }

        $availableDates = CommodityPrice::select('tanggal', DB::raw('count(*) as count'))
            ->groupBy('tanggal')
            ->orderByDesc('tanggal')
            ->take(15)
            ->get();

        $availableKabupaten = MasterData::where('type', 'kabupaten')->orderBy('name')->get();

        $totalRecords = CommodityPrice::count();
        $totalVariants = CommodityPrice::distinct('nama_variant')->count('nama_variant');
        $latestDate = CommodityPrice::max('tanggal');

        return view('prices.index', compact(
            'prices',
            'availableDates',
            'availableKabupaten',
            'totalRecords',
            'totalVariants',
            'latestDate'
        ));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => [
                'required',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['xlsx', 'xls', 'csv', 'txt'])) {
                        $fail('Format file harus berupa .xlsx, .xls, atau .csv.');
                    }
                },
            ],
        ], [
            'file_excel.required' => 'Silakan pilih file Excel/CSV untuk diimpor.',
            'file_excel.max'      => 'Ukuran file tidak boleh lebih dari 10 MB.',
        ]);

        $file = $request->file('file_excel');
        $filePath = $file->getRealPath();

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            // formatData = false agar nilai numerik asli dari Excel tidak diubah menjadi string lokal terformat
            $rows = $sheet->toArray(null, true, false, false);

            if (empty($rows) || count($rows) < 2) {
                return back()->with('error', 'File Excel kosong atau tidak memiliki baris data.');
            }

            $headerRow = $rows[0];

            // Deteksi format template: Baris-per-baris (Format Baru: Kabupaten, Komoditas, Satuan, Tanggal, Harga)
            // vs Matriks Tanggal Horizontal (Format Lama: Nama Variant, Satuan, Tgl 1, Tgl 2, ...)
            $colMap = [
                'kabupaten' => null,
                'komoditas' => null,
                'satuan'    => null,
                'tanggal'   => null,
                'harga'     => null,
            ];

            foreach ($headerRow as $idx => $cell) {
                $c = strtolower(trim((string)$cell));
                if (empty($c)) continue;

                if ($colMap['kabupaten'] === null && (str_contains($c, 'kabupaten') || str_contains($c, 'wilayah') || str_contains($c, 'daerah') || str_contains($c, 'kota'))) {
                    $colMap['kabupaten'] = $idx;
                } elseif ($colMap['komoditas'] === null && (str_contains($c, 'komoditas') || str_contains($c, 'variant') || str_contains($c, 'varian') || str_contains($c, 'barang') || str_contains($c, 'produk'))) {
                    $colMap['komoditas'] = $idx;
                } elseif ($colMap['satuan'] === null && (str_contains($c, 'satuan') || str_contains($c, 'unit'))) {
                    $colMap['satuan'] = $idx;
                } elseif ($colMap['tanggal'] === null && (str_contains($c, 'tanggal') || str_contains($c, 'tgl') || str_contains($c, 'date') || str_contains($c, 'waktu'))) {
                    $colMap['tanggal'] = $idx;
                } elseif ($colMap['harga'] === null && (str_contains($c, 'harga') || str_contains($c, 'price') || str_contains($c, 'nilai') || str_contains($c, 'nominal'))) {
                    $colMap['harga'] = $idx;
                }
            }

            $isNewFormat = ($colMap['tanggal'] !== null && $colMap['harga'] !== null)
                || (isset($headerRow[0]) && strtolower(trim((string)$headerRow[0])) === 'kabupaten');

            $masterKomoditas = MasterData::where('type', 'komoditas')->get();
            $masterKabupaten = MasterData::where('type', 'kabupaten')->get();

            $totalImported = 0;
            $importedDates = [];

            DB::beginTransaction();

            if ($isNewFormat) {
                // ─── Format Baru (Tabel Baris-per-Baris Sesuai Gambar) ───
                $idxKab = $colMap['kabupaten'] ?? 0;
                $idxKom = $colMap['komoditas'] ?? 1;
                $idxSat = $colMap['satuan'] ?? 2;
                $idxTgl = $colMap['tanggal'] ?? 3;
                $idxHrg = $colMap['harga'] ?? 4;

                for ($r = 1; $r < count($rows); $r++) {
                    $row = $rows[$r];
                    if (empty($row) || !array_filter($row)) continue;

                    $namaKabupaten = trim((string)($row[$idxKab] ?? ''));
                    $namaKomoditas = trim((string)($row[$idxKom] ?? ''));
                    if (empty($namaKomoditas)) continue;

                    $satuan = trim((string)($row[$idxSat] ?? 'Rp/Kg'));
                    if (empty($satuan)) $satuan = 'Rp/Kg';

                    $rawTanggal = $row[$idxTgl] ?? null;
                    $tanggalStr = $this->parseRowDate($rawTanggal);
                    if (!$tanggalStr) continue;

                    $rawHarga = $row[$idxHrg] ?? null;
                    $cleanPrice = $this->parsePriceValue($rawHarga);
                    if ($cleanPrice <= 0) continue;

                    $matchedKabupaten = !empty($namaKabupaten) 
                        ? $this->matchMasterKabupaten($namaKabupaten, $masterKabupaten) 
                        : null;
                    $kabupatenId = $matchedKabupaten?->id;

                    $matchedMaster = $this->matchMasterKomoditas($namaKomoditas, $masterKomoditas);
                    $komoditasId = $matchedMaster?->id;

                    CommodityPrice::updateOrCreate(
                        [
                            'nama_variant' => $namaKomoditas,
                            'tanggal'      => $tanggalStr,
                            'kabupaten_id' => $kabupatenId,
                        ],
                        [
                            'komoditas_id'   => $komoditasId,
                            'nama_kabupaten' => $namaKabupaten ?: ($matchedKabupaten ? str_replace('Kabupaten ', '', $matchedKabupaten->name) : null),
                            'satuan'         => $satuan,
                            'harga'          => $cleanPrice,
                            'sumber'         => 'import_excel',
                            'created_by'     => Auth::id(),
                        ]
                    );

                    $totalImported++;
                    $importedDates[$tanggalStr] = true;
                }
            } else {
                // ─── Format Lama (Matriks Tanggal Horizontal) ───
                $dateColumns = [];

                for ($col = 2; $col < count($headerRow); $col++) {
                    $rawDate = trim((string)($headerRow[$col] ?? ''));
                    if (empty($rawDate)) continue;

                    $parsedDate = $this->parseColumnDate($rawDate);
                    if ($parsedDate) {
                        $dateColumns[$col] = $parsedDate;
                    }
                }

                if (empty($dateColumns)) {
                    return back()->with('error', 'Format kolom file Excel tidak dikenali. Silakan unduh format template resmi yang disediakan.');
                }

                for ($r = 1; $r < count($rows); $r++) {
                    $row = $rows[$r];
                    $namaVariant = trim((string)($row[0] ?? ''));
                    if (empty($namaVariant)) continue;

                    $satuan = trim((string)($row[1] ?? 'kg'));
                    if (empty($satuan)) $satuan = 'kg';

                    $matchedMaster = $this->matchMasterKomoditas($namaVariant, $masterKomoditas);
                    $komoditasId = $matchedMaster?->id;

                    foreach ($dateColumns as $colIdx => $tanggalStr) {
                        $rawPrice = $row[$colIdx] ?? null;
                        if ($rawPrice === null || $rawPrice === '') continue;

                        $cleanPrice = $this->parsePriceValue($rawPrice);
                        if ($cleanPrice <= 0) continue;

                        CommodityPrice::updateOrCreate(
                            [
                                'nama_variant' => $namaVariant,
                                'tanggal'      => $tanggalStr,
                                'kabupaten_id' => null,
                            ],
                            [
                                'komoditas_id' => $komoditasId,
                                'satuan'       => $satuan,
                                'harga'        => $cleanPrice,
                                'sumber'       => 'import_excel',
                                'created_by'   => Auth::id(),
                            ]
                        );

                        $totalImported++;
                        $importedDates[$tanggalStr] = true;
                    }
                }
            }

            DB::commit();

            if ($totalImported === 0) {
                return back()->with('error', 'Tidak ada data harga yang valid untuk diimpor. Pastikan format tanggal dan harga terisi dengan benar.');
            }

            $distinctDatesCount = count($importedDates);
            $sortedDates = array_keys($importedDates);
            sort($sortedDates);
            $rangeText = $distinctDatesCount > 1 
                ? (Carbon::parse($sortedDates[0])->format('d/m/Y') . ' s/d ' . Carbon::parse(end($sortedDates))->format('d/m/Y'))
                : (isset($sortedDates[0]) ? Carbon::parse($sortedDates[0])->format('d/m/Y') : '-');

            return redirect()->back(fallback: route('prices.index'))->with(
                'success',
                "Berhasil mengimpor {$totalImported} catatan harga untuk {$distinctDatesCount} tanggal ({$rangeText})!"
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Harga Pasar');

        // Header style
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32'], // Brand Forest Green Sulbar
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D7DE']],
            ],
        ];

        // Format kolom template sesuai gambar: Kabupaten, Komoditas, Satuan, Tanggal, Harga
        $headers = ['Kabupaten', 'Komoditas', 'Satuan', 'Tanggal', 'Harga'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Contoh data template (1 baris contoh)
        $data = [
            ['Majene', 'Bawang Bombai', 'Rp/Kg', '3/08/2026', 35000],
        ];

        $sheet->fromArray($data, null, 'A2');

        $lastRow = count($data) + 1;
        $sheet->getStyle("A2:E{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E0E0E0');
        $sheet->getStyle("C2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E2:E{$lastRow}")->getNumberFormat()->setFormatCode('"Rp"#,##0.00');
        $sheet->getStyle("E2:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        foreach (['A' => 20, 'B' => 24, 'C' => 12, 'D' => 16, 'E' => 18] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Aktifkan auto-filter seperti pada gambar referensi
        $sheet->setAutoFilter("A1:E{$lastRow}");

        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_harga_pasar_sulbar.xlsx';

        return new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'Cache-Control'       => 'max-age=0',
            ]
        );
    }

    public function parseRowDate($rawDate): ?string
    {
        if ($rawDate === null || $rawDate === '') {
            return null;
        }

        if ($rawDate instanceof \DateTimeInterface) {
            return $rawDate->format('Y-m-d');
        }

        // Kasus Excel serial date (angka > 30000, misal 46237)
        if (is_numeric($rawDate) && (int)$rawDate > 30000) {
            try {
                return ExcelDate::excelToDateTimeObject((int)$rawDate)->format('Y-m-d');
            } catch (\Exception $e) {
                // Lanjut ke string parsing
            }
        }

        $str = trim((string)$rawDate);

        // Hapus spasi di sekitar slash / dash / dot (misal: "3 /08 /2026" -> "3/08/2026")
        $normalized = preg_replace('/\s*([\/\-\.])\s*/', '$1', $str);

        // Format D/M/YY (contoh: 3/8/26 atau 12/10/26) -> 2026-10-12
        if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{2})$/', $normalized, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = '20' . $matches[3];
            return "{$year}-{$month}-{$day}";
        }

        // Format D/M/YYYY (contoh: 3/08/2026 atau 20/08/2026) -> 2026-08-20
        if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/', $normalized, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];
            return "{$year}-{$month}-{$day}";
        }

        // Format YYYY-MM-DD
        if (preg_match('/^(\d{4})[\/\-\.](\d{1,2})[\/\-\.](\d{1,2})$/', $normalized, $matches)) {
            $year = $matches[1];
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $day = str_pad($matches[3], 2, '0', STR_PAD_LEFT);
            return "{$year}-{$month}-{$day}";
        }

        try {
            return Carbon::parse($normalized)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseColumnDate(string $rawDate): ?string
    {
        return $this->parseRowDate($rawDate);
    }

    public function parsePriceValue($raw): float
    {
        if ($raw === null || $raw === '') {
            return 0.0;
        }

        // Jika tipe data numerik dari Excel
        if (is_float($raw) || (is_numeric($raw) && str_contains((string)$raw, '.'))) {
            $val = (float) $raw;
            // Jika angka desimal kecil di bawah 1000 (contoh: 40.028 atau 12.15 karena Excel membaca titik sebagai pecahan)
            if ($val > 0 && $val < 1000) {
                return round($val * 1000, 2);
            }
            return round($val, 2);
        }

        if (is_int($raw) || (is_numeric($raw) && !str_contains((string)$raw, '.'))) {
            return (float) $raw;
        }

        $str = trim((string) $raw);
        // Hapus awalan dan akhiran yang bukan angka (misal Rp, Rp., spasi)
        $cleaned = preg_replace('/^[^0-9]+/', '', $str);
        $cleaned = preg_replace('/[^0-9]+$/', '', $cleaned);
        // Bersihkan karakter selain angka, titik, dan koma
        $cleaned = preg_replace('/[^0-9,.]/', '', $cleaned);
        if ($cleaned === '') {
            return 0.0;
        }

        // 1. Kasus ribuan titik + desimal koma: 40.028,50 atau 35.000,00
        if (preg_match('/^\d{1,3}(\.\d{3})+,\d+$/', $cleaned)) {
            $cleaned = str_replace('.', '', $cleaned);
            $cleaned = str_replace(',', '.', $cleaned);
            return (float) $cleaned;
        }

        // 2. Kasus ribuan koma + desimal titik: 40,028.50
        if (preg_match('/^\d{1,3}(,\d{3})+\.\d+$/', $cleaned)) {
            $cleaned = str_replace(',', '', $cleaned);
            return (float) $cleaned;
        }

        // 3. Kasus ribuan titik murni: 40.028 atau 117.778
        if (preg_match('/^\d{1,3}(\.\d{3})+$/', $cleaned)) {
            return (float) str_replace('.', '', $cleaned);
        }

        // 4. Kasus ribuan koma murni: 40,028 atau 117,778
        if (preg_match('/^\d{1,3}(,\d{3})+$/', $cleaned)) {
            return (float) str_replace(',', '', $cleaned);
        }

        // 5. Kasus ada koma tunggal
        if (str_contains($cleaned, ',')) {
            $parts = explode(',', $cleaned);
            if (count($parts) === 2) {
                if (strlen($parts[1]) === 3 && strlen($parts[0]) <= 3) {
                    return (float) ($parts[0] . $parts[1]);
                }
                return (float) ($parts[0] . '.' . $parts[1]);
            }
        }

        // 6. Kasus ada titik tunggal
        if (str_contains($cleaned, '.')) {
            $parts = explode('.', $cleaned);
            if (count($parts) === 2) {
                if (strlen($parts[1]) === 3 && strlen($parts[0]) <= 3) {
                    return (float) ($parts[0] . $parts[1]);
                }
                $f = (float) $cleaned;
                if ($f > 0 && $f < 1000 && strlen($parts[1]) <= 3) {
                    return round($f * 1000, 2);
                }
                return (float) $cleaned;
            }
        }

        return (float) str_replace(['.', ','], '', $cleaned);
    }

    private function matchMasterKomoditas(string $variantName, $masterKomoditas): ?MasterData
    {
        $vLower = strtolower(trim($variantName));

        // 1. Cek kecocokan persis
        foreach ($masterKomoditas as $m) {
            if (strtolower(trim($m->name)) === $vLower) {
                return $m;
            }
        }

        // 2. Cek kecocokan parsial (misal "Bawang Putih Honan" cocok dengan "Bawang Putih")
        foreach ($masterKomoditas as $m) {
            $mLower = strtolower(trim($m->name));
            if (str_contains($vLower, $mLower) || str_contains($mLower, $vLower)) {
                return $m;
            }
        }

        return null;
    }

    private function matchMasterKabupaten(?string $name, $masterKabupaten): ?MasterData
    {
        if (empty($name)) return null;

        $clean = strtolower(trim(str_replace(['kabupaten', 'kab.', 'kab'], '', $name)));

        // 1. Cek kecocokan persis atau setelah strip kata "Kabupaten"
        foreach ($masterKabupaten as $k) {
            $kClean = strtolower(trim(str_replace(['kabupaten', 'kab.', 'kab'], '', $k->name)));
            if ($kClean === $clean) {
                return $k;
            }
        }

        // 2. Cek kecocokan parsial
        foreach ($masterKabupaten as $k) {
            $kClean = strtolower(trim(str_replace(['kabupaten', 'kab.', 'kab'], '', $k->name)));
            if (str_contains($clean, $kClean) || str_contains($kClean, $clean)) {
                return $k;
            }
        }

        // 3. Alias populer di wilayah Sulawesi Barat
        $aliases = [
            'polman'     => 'Kabupaten Polewali Mandar',
            'mateng'     => 'Kabupaten Mamuju Tengah',
            'pasangkayu' => 'Kabupaten Pasangkayu',
            'mamuju'     => 'Kabupaten Mamuju',
            'majene'     => 'Kabupaten Majene',
            'mamasa'     => 'Kabupaten Mamasa',
        ];

        if (isset($aliases[$clean])) {
            foreach ($masterKabupaten as $k) {
                if ($k->name === $aliases[$clean]) {
                    return $k;
                }
            }
        }

        return null;
    }
}
