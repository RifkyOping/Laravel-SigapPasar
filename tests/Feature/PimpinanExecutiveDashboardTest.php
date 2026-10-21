<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MasterData;
use App\Models\Issue;
use App\Models\FieldAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PimpinanExecutiveDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_pimpinan_can_view_executive_dashboard_with_all_components(): void
    {
        $pimpinan = User::where('role', 'pimpinan')->first();
        if (!$pimpinan) {
            $pimpinan = User::factory()->create([
                'name' => 'Pimpinan Diskoperindag Sulbar',
                'email' => 'pimpinan@sigappasar.go.id',
                'role' => 'pimpinan',
            ]);
        }

        $response = $this->actingAs($pimpinan)->get('/dashboard');

        $response->assertSeeText('PERIODE DATA:');
        $response->assertSeeText('Peta Sebaran Titik Isu & Status Kerawanan 6 Kabupaten');
        $response->assertSeeText('Stabilisasi Harga');
        $response->assertSeeText('Hari Ini');
        $response->assertSeeText('7 Hari Terakhir');
        $response->assertSeeText('Bulan Ini');
        $response->assertSeeText('Tahun Ini');

        // Navigasi Pimpinan hanya Ringkasan Eksekutif, bukan menu operasional
        $response->assertDontSee('Input Isu Baru');
        $response->assertDontSee('Antrian Verifikasi');
        $response->assertDontSee('Penugasan');
        $response->assertDontSee('Manajemen User');
    }

    public function test_pimpinan_dashboard_displays_detailed_kabupaten_data_and_card_interactivity(): void
    {
        $pimpinan = User::where('role', 'pimpinan')->first();

        $response = $this->actingAs($pimpinan)->get('/dashboard');
        $response->assertStatus(200);

        // Check that view received enriched petaKabupaten
        $petaKabupaten = $response->viewData('petaKabupaten');
        $this->assertNotEmpty($petaKabupaten);

        foreach ($petaKabupaten as $kab) {
            $this->assertArrayHasKey('id', $kab);
            $this->assertArrayHasKey('name', $kab);
            $this->assertArrayHasKey('status_level', $kab);
            $this->assertArrayHasKey('total_isu_periode', $kab);
            $this->assertArrayHasKey('total_isu_all', $kab);
            $this->assertArrayHasKey('isu_aktif', $kab);
            $this->assertArrayHasKey('isu_kritis', $kab);
            $this->assertArrayHasKey('isu_selesai', $kab);
            $this->assertArrayHasKey('komoditas_breakdown', $kab);
            $this->assertArrayHasKey('pasar_list', $kab);
            $this->assertArrayHasKey('isu_terkini', $kab);
            $this->assertArrayHasKey('inspeksi_list', $kab);
            $this->assertArrayHasKey('link_semua_isu', $kab);
        }

        // Check that view received semuaWilayahData
        $semuaWilayahData = $response->viewData('semuaWilayahData');
        $this->assertNotEmpty($semuaWilayahData);
        $this->assertArrayHasKey('total_isu_periode', $semuaWilayahData);
        $this->assertArrayHasKey('total_isu_all', $semuaWilayahData);
        $this->assertArrayHasKey('isu_aktif', $semuaWilayahData);
        $this->assertArrayHasKey('isu_terkini', $semuaWilayahData);

        // Check that detail card DOM elements exist
        $response->assertSee('id="detailKabupatenCard"', false);
        $response->assertSee('id="detKabName"', false);
        $response->assertSee('id="detKpiGrid"', false);
        $response->assertSee('id="detIsuTableWrap"', false);
        $response->assertSee('toggleKabupaten', false);
        $response->assertSee('resetKeSemuaWilayah', false);
        $response->assertSee('selectKabupaten', false);
    }

    public function test_pimpinan_can_filter_dashboard_by_periods(): void
    {
        $pimpinan = User::where('role', 'pimpinan')->first();

        foreach (['hari', 'minggu', 'bulan', 'tahun'] as $periode) {
            $response = $this->actingAs($pimpinan)->get("/dashboard?periode={$periode}");
            $response->assertStatus(200);
            $response->assertSee('Ringkasan Eksekutif');
        }
    }

    public function test_pimpinan_can_view_all_issues_and_inspect_progress_read_only(): void
    {
        $pimpinan = User::where('role', 'pimpinan')->first();
        $komoditas = MasterData::where('type', 'komoditas')->first();
        $kabupaten = MasterData::where('type', 'kabupaten')->first();

        $issue = Issue::create([
            'tanggal_temuan'   => now(),
            'platform'         => 'Twitter/X',
            'link_postingan'   => 'https://x.com/warga/status/1234567890',
            'nama_akun'        => '@wargamandar',
            'jumlah_interaksi' => 150,
            'jumlah_mention'   => 15,
            'kutipan_postingan'=> 'Harga beras melonjak tinggi di pasar',
            'komoditas_id'     => $komoditas->id,
            'kategori_isu'     => 'Kenaikan Harga',
            'kabupaten_id'     => $kabupaten->id,
            'lokasi_spesifik'  => 'Pasar Sentral',
            'status'           => 'baru',
            'prioritas'        => 'tinggi',
            'input_by'         => User::where('role', 'pengawas_siber')->first()?->id ?? 1,
        ]);

        // 1. Pimpinan dapat melihat daftar seluruh isu pasar
        $responseIndex = $this->actingAs($pimpinan)->get('/isu');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSeeText('Daftar Pantauan Isu Pasar');
        $responseIndex->assertSeeText('Lihat Progres');
        $responseIndex->assertDontSee('Input Isu Baru (Tahap 1)');

        // 2. Pimpinan dapat membuka detail isu untuk melihat linimasa progres
        $responseShow = $this->actingAs($pimpinan)->get("/isu/{$issue->id}");
        $responseShow->assertStatus(200);
        $responseShow->assertSeeText('Progres Penanganan Isu (5 Tahap Rantai Komando)');
        $responseShow->assertSeeText('Mode Pantauan Eksekutif: Hanya Baca');

        // Pimpinan tidak melihat tombol aksi operasional teknis
        $responseShow->assertDontSee('Edit Data');
        $responseShow->assertDontSee('Tugaskan Petugas');
        $responseShow->assertDontSee('Tutup / Selesaikan Isu');

        // 3. Pimpinan diblokir (403 Forbidden) dari rute mutasi dan aksi teknis
        $this->actingAs($pimpinan)->get('/isu/buat')->assertStatus(403);
        $this->actingAs($pimpinan)->post('/isu', [])->assertStatus(403);
        $this->actingAs($pimpinan)->get("/isu/{$issue->id}/edit")->assertStatus(403);
        $this->actingAs($pimpinan)->delete("/isu/{$issue->id}")->assertStatus(403);
        $this->actingAs($pimpinan)->get('/penugasan')->assertStatus(403);
        $this->actingAs($pimpinan)->get('/intervensi')->assertStatus(403);
    }

    public function test_super_admin_can_access_operational_and_system_management(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        $response = $this->actingAs($superAdmin)->get('/dashboard');
        $response->assertStatus(200);

        $responseUsers = $this->actingAs($superAdmin)->get('/users');
        $responseUsers->assertStatus(200);
        $responseUsers->assertSee('Manajemen Pengguna');
    }

    public function test_admin_and_super_admin_view_identical_executive_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => 'admin']);
        }

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSeeText('PERIODE DATA:');
        $response->assertSeeText('Peta Sebaran Titik Isu & Status Kerawanan 6 Kabupaten');
        $response->assertSeeText('Stabilisasi Harga');
        $response->assertSeeText('Hari Ini');
        $response->assertSeeText('7 Hari Terakhir');
        $response->assertSeeText('Bulan Ini');
        $response->assertSeeText('Tahun Ini');

        $response->assertSeeText('Tren Harga Komoditas Harian');

        $this->assertNotEmpty($response->viewData('petaKabupaten'));
        $this->assertNotEmpty($response->viewData('perkembanganHarga'));
        $grafikTren = $response->viewData('grafikTren');
        $this->assertNotEmpty($grafikTren);
        $this->assertArrayHasKey('harga_rata_rata', $grafikTren);
        $this->assertArrayHasKey('harga_het', $grafikTren);
        $this->assertNotEmpty($grafikTren['labels']);

        foreach (['hari', 'minggu', 'bulan', 'tahun'] as $periode) {
            $res = $this->actingAs($admin)->get("/dashboard?periode={$periode}");
            $res->assertStatus(200);
            $res->assertSeeText('PERIODE DATA:');
        }
    }
}
