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
        $query = CommodityPrice::with('komoditas')->latest('tanggal');

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        }

        if ($request->filled('search')) {
            $query->where('nama_variant', 'like', "%{$request->search}%");
        }

        $prices = $query->paginate(25)->withQueryString();

        $availableDates = CommodityPrice::select('tanggal', DB::raw('count(*) as count'))
            ->groupBy('tanggal')
            ->orderByDesc('tanggal')
            ->take(15)
            ->get();

        $totalRecords = CommodityPrice::count();
        $totalVariants = CommodityPrice::distinct('nama_variant')->count('nama_variant');
        $latestDate = CommodityPrice::max('tanggal');

        return view('prices.index', compact(
            'prices',
            'availableDates',
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
            // Gunakan formatData = false agar nilai numerik asli dari Excel tidak diubah menjadi string terformat lokal
            $rows = $sheet->toArray(null, true, false, false);

            if (empty($rows) || count($rows) < 2) {
                return back()->with('error', 'File Excel kosong atau tidak memiliki baris data.');
            }

            // Header row: [0 => Nama Variant, 1 => Satuan, 2..N => Tanggal]
            $headerRow = $rows[0];
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
                return back()->with('error', 'Kolom tanggal tidak ditemukan pada baris judul file Excel. Pastikan kolom ke-3 dan seterusnya berisi tanggal (contoh: 12/10/26 atau 12/10/2026).');
            }

            // Cache master komoditas untuk pemetaan cepat
            $masterKomoditas = MasterData::where('type', 'komoditas')->get();

            $totalImported = 0;
            $importedDates = [];

            DB::beginTransaction();

            for ($r = 1; $r < count($rows); $r++) {
                $row = $rows[$r];
                $namaVariant = trim((string)($row[0] ?? ''));
                if (empty($namaVariant)) continue;

                $satuan = trim((string)($row[1] ?? 'kg'));
                if (empty($satuan)) $satuan = 'kg';

                // Cari kecocokan komoditas di master data
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

            DB::commit();

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

        // Headers matching the screenshot: Nama Variant, Satuan, Dates
        $headers = ['Nama Variant', 'Satuan', '12/10/26', '13/10/26', '14/10/26', '15/10/26', '16/10/26'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Sample data identical to user's screenshot
        $data = [
            ['Bawang Bombai', 'Rp/Kg', 40028, 39472, 39472, 39472, 39472],
            ['Bawang Merah', 'Rp/Kg', 36806, 36694, 36694, 36694, 36694],
            ['Bawang Putih Honan', 'Rp/Kg', 39167, 39056, 39056, 39056, 39056],
            ['Bawang Putih Kating', 'Rp/Kg', 42500, 42500, 42500, 42500, 42500],
            ['Beras Medium', 'Rp/Kg', 13808, 13808, 13808, 13808, 13808],
            ['Beras Premium', 'Rp/Kg', 15180, 15180, 15180, 15180, 15180],
            ['Beras SPHP Bulog', 'Rp/Kg', 12150, 12150, 12150, 12150, 12150],
            ['Cabai Merah Besar', 'Rp/Kg', 74400, 74733, 73733, 72733, 72733],
            ['Cabai Merah Keriting', 'Rp/Kg', 83861, 84583, 85278, 87500, 88889],
            ['Cabai Rawit Hijau', 'Rp/Kg', 89000, 101200, 100533, 100533, 102200],
            ['Cabai Rawit Merah', 'Rp/Kg', 117778, 117222, 115278, 115278, 115278],
            ['Daging Ayam Kampung', 'Rp/Ekor', 82083, 82083, 82083, 82083, 82083],
            ['Daging Ayam Ras', 'Rp/Kg', 40717, 40717, 41050, 40697, 40697],
            ['Daging Sapi Impor Beku', 'Rp/Kg', 149900, 149900, 149900, 149900, 149900],
            ['Daging Sapi Paha Belakang', 'Rp/Kg', 142000, 142000, 142000, 142000, 142000],
            ['Daging Sapi Paha Depan', 'Rp/Kg', 142500, 142500, 142500, 142500, 142500],
            ['Gas LPG 3kg', 'Rp/Tabung', 20000, 20000, 20000, 20000, 20000],
            ['BBM', 'Rp/Liter', 10000, 10000, 10000, 10000, 10000],
        ];

        $sheet->fromArray($data, null, 'A2');

        // Number format and column sizing
        $lastRow = count($data) + 1;
        $sheet->getStyle("C2:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("A2:G{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E0E0E0');

        foreach (['A' => 28, 'B' => 12, 'C' => 14, 'D' => 14, 'E' => 14, 'F' => 14, 'G' => 14] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

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

    private function parseColumnDate(string $rawDate): ?string
    {
        $rawDate = trim($rawDate);

        // Kasus Excel serial date (misal 46307)
        if (is_numeric($rawDate) && (int)$rawDate > 30000) {
            try {
                return ExcelDate::excelToDateTimeObject((int)$rawDate)->format('Y-m-d');
            } catch (\Exception $e) {
                // Lanjut ke parser string
            }
        }

        // Format DD/MM/YY (contoh: 12/10/26) -> 2026-10-12
        if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{2})$/', $rawDate, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = '20' . $matches[3];
            return "{$year}-{$month}-{$day}";
        }

        // Format DD/MM/YYYY (contoh: 12/10/2026) -> 2026-10-12
        if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/', $rawDate, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];
            return "{$year}-{$month}-{$day}";
        }

        // Format YYYY-MM-DD
        if (preg_match('/^(\d{4})[\/\-\.](\d{1,2})[\/\-\.](\d{1,2})$/', $rawDate, $matches)) {
            $year = $matches[1];
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $day = str_pad($matches[3], 2, '0', STR_PAD_LEFT);
            return "{$year}-{$month}-{$day}";
        }

        try {
            return Carbon::parse($rawDate)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
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
            // Di komoditas pasar pangan, harga puluhan atau ribuan rupiah tidak mungkin Rp 40 atau Rp 12
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

        // 1. Kasus ribuan titik + desimal koma: 40.028,50
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
                // Jika tepat 3 digit di belakang koma dan depan 1-3 digit (cth: 40,028 atau 12,150)
                if (strlen($parts[1]) === 3 && strlen($parts[0]) <= 3) {
                    return (float) ($parts[0] . $parts[1]);
                }
                // Jika bukan 3 digit (cth: 40028,50 atau 40028,5), ini desimal
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
        $vLower = strtolower($variantName);

        // 1. Cek kecocokan persis
        foreach ($masterKomoditas as $m) {
            if (strtolower($m->name) === $vLower) {
                return $m;
            }
        }

        // 2. Cek kecocokan parsial (misal "Bawang Putih Honan" cocok dengan "Bawang Putih")
        foreach ($masterKomoditas as $m) {
            $mLower = strtolower($m->name);
            if (str_contains($vLower, $mLower) || str_contains($mLower, $vLower)) {
                return $m;
            }
        }

        return null;
    }
}
