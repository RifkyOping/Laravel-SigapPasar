<?php

namespace Tests\Feature;

use App\Models\CommodityPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class PriceImportWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_download_excel_template(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        $response = $this->actingAs($superAdmin)->get('/harga-pasar/template');

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="template_import_harga_pasar_sulbar.xlsx"');
    }

    public function test_pimpinan_is_strictly_read_only_without_import_export_features(): void
    {
        $pimpinan = User::where('role', 'pimpinan')->first();

        // Pimpinan dapat membaca data harga pasar
        $response = $this->actingAs($pimpinan)->get('/harga-pasar');
        $response->assertStatus(200);
        $response->assertSeeText('Data Pantauan Harga Pasar Harian');
        $response->assertDontSee('Import Excel Harga');
        $response->assertDontSee('Unggah File Spreadsheet');
        $response->assertDontSee('Unduh Format Excel');

        // Pimpinan tidak diizinkan mengakses rute template dan import (403)
        $responseTemplate = $this->actingAs($pimpinan)->get('/harga-pasar/template');
        $responseTemplate->assertStatus(403);

        $responseImport = $this->actingAs($pimpinan)->post('/harga-pasar/import', []);
        $responseImport->assertStatus(403);

        // Dashboard Pimpinan bersih dan mudah dibaca tanpa tombol import/ekspor
        $responseDashboard = $this->actingAs($pimpinan)->get('/dashboard');
        $responseDashboard->assertStatus(200);
        $responseDashboard->assertDontSee('Import Excel Harga');
        $responseDashboard->assertDontSee('Unduh Format');
        $responseDashboard->assertSeeText('PERIODE DATA:');
    }

    public function test_unauthorized_role_cannot_access_import_or_price_pages(): void
    {
        $petugas = User::where('role', 'petugas_lapangan')->first();

        $response = $this->actingAs($petugas)->get('/harga-pasar');
        $response->assertStatus(403);

        $responseImport = $this->actingAs($petugas)->post('/harga-pasar/import', []);
        $responseImport->assertStatus(403);
    }

    public function test_super_admin_can_upload_excel_and_import_daily_prices(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        // Buat file Excel sederhana sesuai format gambar
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['Nama Variant', 'Satuan', '12/10/26', '13/10/26'],
            ['Bawang Bombai', 'Rp/Kg', '40.028', '39.472'],
            ['Cabai Rawit Merah', 'Rp/Kg', '117.778', '117.222'],
            ['Gas LPG 3kg', 'Rp/Tabung', '20.000', '20.000'],
            ['BBM', 'Rp/Liter', '10.000', '10.000'],
        ]);

        $tempPath = tempnam(sys_get_temp_dir(), 'test_price_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'test_harga_sulbar.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($superAdmin)->post('/harga-pasar/import', [
            'file_excel' => $uploadedFile,
        ]);

        $response->assertRedirect('/harga-pasar');
        $response->assertSessionHas('success');

        // Pastikan record tersimpan dengan benar di database
        $bawang = CommodityPrice::where('nama_variant', 'Bawang Bombai')
            ->whereDate('tanggal', '2026-10-12')
            ->first();
        $this->assertNotNull($bawang);
        $this->assertEquals(40028, (int) $bawang->harga);

        $cabai = CommodityPrice::where('nama_variant', 'Cabai Rawit Merah')
            ->whereDate('tanggal', '2026-10-13')
            ->first();
        $this->assertNotNull($cabai);
        $this->assertEquals(117222, (int) $cabai->harga);

        $lpg = CommodityPrice::where('nama_variant', 'Gas LPG 3kg')
            ->whereDate('tanggal', '2026-10-12')
            ->first();
        $this->assertNotNull($lpg);
        $this->assertEquals(20000, (int) $lpg->harga);

        // Pastikan re-import mengupdate tanpa duplikasi
        $tempPath2 = tempnam(sys_get_temp_dir(), 'test_price_2_') . '.xlsx';
        $writer->save($tempPath2);
        $uploadedFile2 = new UploadedFile(
            $tempPath2,
            'test_harga_sulbar.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response2 = $this->actingAs($superAdmin)
            ->from('/harga-pasar')
            ->post('/harga-pasar/import', [
                'file_excel' => $uploadedFile2,
            ]);
        $response2->assertRedirect('/harga-pasar');
        $response2->assertSessionHas('success');

        $bawangBombaiCount = CommodityPrice::where('nama_variant', 'Bawang Bombai')
            ->whereDate('tanggal', '2026-10-12')
            ->count();
        $this->assertEquals(1, $bawangBombaiCount);

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
        if (file_exists($tempPath2)) {
            unlink($tempPath2);
        }
    }

    public function test_pimpinan_dashboard_integrates_imported_prices(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $pimpinan = User::where('role', 'pimpinan')->first();

        // Seed record CommodityPrice
        CommodityPrice::create([
            'nama_variant' => 'Cabai Rawit Merah',
            'satuan'       => 'Rp/Kg',
            'tanggal'      => now()->toDateString(),
            'harga'        => 125000,
            'sumber'       => 'import_excel',
            'created_by'   => $superAdmin->id,
        ]);

        $response = $this->actingAs($pimpinan)->get('/dashboard?periode=hari');
        $response->assertStatus(200);
        $response->assertSee('Import Pasar');
        $response->assertSee('125.000');
    }

    public function test_price_import_handles_comma_and_dot_separators_and_decimals(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menguji berbagai variasi format angka dari Excel:
        // 1. Ribuan format koma '40,028'
        // 2. Ribuan format titik '36.806'
        // 3. Format desimal dengan koma '40.028,50'
        // 4. Format desimal dengan titik '117,778.75'
        // 5. Angka numerik asli dengan cell format #,##0
        $sheet->fromArray([
            ['Nama Variant', 'Satuan', '12/10/26'],
            ['Bawang Bombai', 'Rp/Kg', '40,028'],
            ['Bawang Merah', 'Rp/Kg', '36.806'],
            ['Cabai Rawit Merah', 'Rp/Kg', '40.028,50'],
            ['Cabai Rawit Hijau', 'Rp/Kg', '117,778.75'],
        ]);

        $tempPath = tempnam(sys_get_temp_dir(), 'test_price_fmt_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'test_fmt.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($superAdmin)->post('/harga-pasar/import', [
            'file_excel' => $uploadedFile,
        ]);
        $response->assertRedirect('/harga-pasar');
        $response->assertSessionHas('success');

        // Pastikan nilai ribuan dengan koma tersimpan 40028, bukan 40.03 / 40
        $bombai = CommodityPrice::where('nama_variant', 'Bawang Bombai')->whereDate('tanggal', '2026-10-12')->first();
        $this->assertNotNull($bombai);
        $this->assertEquals(40028.00, (float) $bombai->harga);

        // Pastikan nilai ribuan dengan titik tersimpan 36806
        $merah = CommodityPrice::where('nama_variant', 'Bawang Merah')->whereDate('tanggal', '2026-10-12')->first();
        $this->assertNotNull($merah);
        $this->assertEquals(36806.00, (float) $merah->harga);

        // Pastikan nilai pecahan desimal koma tersimpan 40028.50
        $cabaiMerah = CommodityPrice::where('nama_variant', 'Cabai Rawit Merah')->whereDate('tanggal', '2026-10-12')->first();
        $this->assertNotNull($cabaiMerah);
        $this->assertEquals(40028.50, (float) $cabaiMerah->harga);

        // Pastikan nilai pecahan desimal titik tersimpan 117778.75
        $cabaiHijau = CommodityPrice::where('nama_variant', 'Cabai Rawit Hijau')->whereDate('tanggal', '2026-10-12')->first();
        $this->assertNotNull($cabaiHijau);
        $this->assertEquals(117778.75, (float) $cabaiHijau->harga);

        // Pastikan halaman tampilan /harga-pasar menampilkan format rupiah bulat tanpa angka di belakang koma
        $viewResponse = $this->actingAs($superAdmin)->get('/harga-pasar?tanggal=2026-10-12');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('40.028');
        $viewResponse->assertSee('36.806');
        $viewResponse->assertDontSee('40.028,50');
        $viewResponse->assertDontSee('117.778,75');
        // Pastikan kolom sumber tidak menggunakan underscore
        $viewResponse->assertSee('Import Excel');
        $viewResponse->assertDontSee('IMPORT_EXCEL');

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
    }

    public function test_user_can_customize_number_of_prices_per_page_or_view_all(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        // 1. Default per page (25)
        $responseDefault = $this->actingAs($superAdmin)->get('/harga-pasar');
        $responseDefault->assertStatus(200);
        $this->assertCount(25, $responseDefault->viewData('prices'));

        // 2. Custom per page: 10
        $response10 = $this->actingAs($superAdmin)->get('/harga-pasar?per_page=10');
        $response10->assertStatus(200);
        $this->assertCount(10, $response10->viewData('prices'));

        // 3. Custom per page: 50
        $response50 = $this->actingAs($superAdmin)->get('/harga-pasar?per_page=50');
        $response50->assertStatus(200);
        $this->assertCount(50, $response50->viewData('prices'));

        // 4. View all (per_page=all)
        $totalRecords = CommodityPrice::count();
        $responseAll = $this->actingAs($superAdmin)->get('/harga-pasar?per_page=all');
        $responseAll->assertStatus(200);
        $this->assertCount($totalRecords, $responseAll->viewData('prices'));
        $responseAll->assertSee('Mode Semua Data Aktif');
    }

    public function test_super_admin_can_download_and_import_new_template_format_with_kabupaten(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        // 1. Verifikasi download template mengembalikan header dan format baru
        $responseTemplate = $this->actingAs($superAdmin)->get('/harga-pasar/template');
        $responseTemplate->assertStatus(200);

        // 2. Buat spreadsheet sesuai format baru pada gambar referensi
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Kabupaten', 'Komoditas', 'Satuan', 'Tanggal', 'Harga'],
            ['Majene', 'Bawang Bombai', 'Rp/Kg', '3 /08 /2026', 'Rp35.000,00'],
            ['Majene', 'Bawang Bombai', 'Rp/Kg', '4 /08 /2026', 'Rp35.000,00'],
            ['Mamuju', 'Bawang Bombai', 'Rp/Kg', '3 /08 /2026', 'Rp38.000,00'],
            ['Majene', 'Bawang Bombai', 'Rp/Kg', '20 /08 /2026', 'Rp36.667,00'],
        ]);

        $tempPath = tempnam(sys_get_temp_dir(), 'test_new_fmt_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'test_harga_baru.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        // 3. Post import file
        $responseImport = $this->actingAs($superAdmin)
            ->from('/harga-pasar')
            ->post('/harga-pasar/import', [
                'file_excel' => $uploadedFile,
            ]);

        $responseImport->assertRedirect('/harga-pasar');
        $responseImport->assertSessionHas('success');

        // 4. Verifikasi data tersimpan di database dengan relasi kabupaten
        $majenePrice = CommodityPrice::where('nama_variant', 'Bawang Bombai')
            ->where('nama_kabupaten', 'Majene')
            ->whereDate('tanggal', '2026-08-03')
            ->first();

        $this->assertNotNull($majenePrice);
        $this->assertEquals(35000, (int) $majenePrice->harga);
        $this->assertEquals('Majene', $majenePrice->nama_kabupaten);
        $this->assertEquals('Kabupaten Majene', $majenePrice->kabupaten?->name);

        $mamujuPrice = CommodityPrice::where('nama_variant', 'Bawang Bombai')
            ->where('nama_kabupaten', 'Mamuju')
            ->whereDate('tanggal', '2026-08-03')
            ->first();

        $this->assertNotNull($mamujuPrice);
        $this->assertEquals(38000, (int) $mamujuPrice->harga);

        $majeneAug20 = CommodityPrice::where('nama_variant', 'Bawang Bombai')
            ->where('nama_kabupaten', 'Majene')
            ->whereDate('tanggal', '2026-08-20')
            ->first();

        $this->assertNotNull($majeneAug20);
        $this->assertEquals(36667, (int) $majeneAug20->harga);

        // 5. Verifikasi tampilan web menampilkan kolom Kabupaten dan dapat difilter
        $viewResponse = $this->actingAs($superAdmin)->get('/harga-pasar');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('<th>Kabupaten</th>', false);
        $viewResponse->assertSee('<th>Komoditas</th>', false);
        $viewResponse->assertSee('Majene');
        $viewResponse->assertSee('Mamuju');

        // Filter berdasarkan kabupaten
        $filterResponse = $this->actingAs($superAdmin)->get('/harga-pasar?kabupaten=' . $majenePrice->kabupaten_id);
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Majene');

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
    }
}

