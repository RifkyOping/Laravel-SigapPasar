<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MasterData;
use App\Models\Issue;
use App\Models\FieldAssignment;
use App\Models\Intervention;
use App\Models\Resolution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SigapPasarWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);
    }

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SIGAP');
        $response->assertSee('PASAR');
        $response->assertSee('Simulasi Radar Isu Pangan');
    }

    public function test_super_admin_can_access_dashboard_and_management(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => 'super_admin']);
        }

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertStatus(200)
            ->assertSee('Dashboard Analitik');

        $this->actingAs($admin)
            ->get('/master-data')
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get('/users')
            ->assertStatus(200);
    }

    public function test_complete_6_stages_workflow(): void
    {
        // 1. Setup users and master data
        $pengawas = User::where('role', 'pengawas_siber')->first();
        $verifikator = User::where('role', 'verifikator')->first();
        $admin = User::where('role', 'admin')->first();
        $petugas = User::where('role', 'petugas_lapangan')->first();
        $komoditas = MasterData::where('type', 'komoditas')->first();
        $kabupaten = MasterData::where('type', 'kabupaten')->first();

        $this->assertNotNull($pengawas);
        $this->assertNotNull($verifikator);
        $this->assertNotNull($admin);
        $this->assertNotNull($petugas);
        $this->assertNotNull($komoditas);
        $this->assertNotNull($kabupaten);

        // TAHAP 1: Pengawas Siber opens create issue page
        $this->actingAs($pengawas)
            ->get('/isu/buat')
            ->assertStatus(200)
            ->assertSee('Input Deteksi Isu Siber (Tahap 1)');

        // Alias /isu/create redirects to /isu/buat
        $this->actingAs($pengawas)
            ->get('/isu/create')
            ->assertRedirect(route('issues.create'));

        // TAHAP 1: Pengawas Siber inputs issue
        $issueData = [
            'tanggal_temuan'    => now()->format('Y-m-d H:i:s'),
            'platform'          => 'Twitter/X',
            'link_postingan'    => 'https://x.com/wargasby/status/123456',
            'nama_akun'         => '@wargasby',
            'jumlah_interaksi'  => 12500,
            'jumlah_mention'    => 450,
            'kutipan_postingan' => 'Harga cabai rawit di pasar naik drastis tembus 80rb per kilo!',
            'komoditas_id'      => $komoditas->id,
            'kategori_isu'      => 'lonjakan_harga',
            'kabupaten_id'      => $kabupaten->id,
            'catatan_input'     => 'Viral di linimasa siber warga lokal',
        ];

        $response = $this->actingAs($pengawas)->post('/isu', $issueData);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $issue = Issue::latest()->first();
        $this->assertEquals('baru', $issue->status);
        $this->assertGreaterThan(0, $issue->virality_score);

        // TAHAP 2: Verifikator verifies report
        $verifData = [
            'hasil_verifikasi'      => 'valid',
            'prioritas_rekomendasi' => 'kritis',
            'catatan_verifikasi'    => 'Laporan terkonfirmasi ramai diperbincangkan warganet lokal.',
            'saran_tindakan'        => 'Segera cek kondisi stok di pasar induk.',
        ];

        $responseVerif = $this->actingAs($verifikator)->post("/verifikasi/{$issue->id}", $verifData);
        $responseVerif->assertSessionHasNoErrors();
        $responseVerif->assertRedirect(route('verifikasi.index'));

        $issue->refresh();
        $this->assertEquals('diverifikasi', $issue->status);
        $this->assertEquals('kritis', $issue->prioritas);

        // TAHAP 3: Admin assigns field officer
        $assignData = [
            'petugas_id'        => $petugas->id,
            'tanggal_penugasan' => now()->toDateString(),
            'catatan_admin'     => 'Cek harga grosir dan lapak pedagang.',
        ];

        $responseAssign = $this->actingAs($admin)->post("/penugasan/{$issue->id}", $assignData);
        $responseAssign->assertSessionHasNoErrors();

        $issue->refresh();
        $this->assertEquals('ditugaskan', $issue->status);
        $assignment = FieldAssignment::where('issue_id', $issue->id)->first();
        $this->assertNotNull($assignment);

        // TAHAP 4: Petugas Lapangan check-in GPS & submits field report
        $checkinResponse = $this->actingAs($petugas)->post("/laporan/{$assignment->id}/checkin", [
            'latitude'  => -7.2575,
            'longitude' => 112.7521,
        ]);
        $checkinResponse->assertSessionHasNoErrors();

        $laporanData = [
            'harga_aktual'      => 75000,
            'tanggal_kunjungan' => now()->toDateString(),
            'catatan_temuan'    => 'Stok cabai menipis dari tengkulak luar kota karena cuaca hujan deras.',
            'catatan_wawancara' => 'Pedagang menyatakan pasokan berkurang separuh sejak dua hari lalu.',
        ];

        $responseLaporan = $this->actingAs($petugas)->post("/laporan/{$assignment->id}/submit", $laporanData);
        $responseLaporan->assertSessionHasNoErrors();

        $assignment->refresh();
        $this->assertEquals('selesai', $assignment->status);
        $this->assertEquals(75000, $assignment->harga_aktual);

        // TAHAP 5: Admin launches intervention
        $intvData = [
            'jenis_intervensi'  => 'operasi_pasar',
            'nama_intervensi'   => 'Operasi Pasar Cabai Murah Mandiri',
            'deskripsi'         => 'Penyaluran pasokan cabai 2 ton dengan harga subsidi.',
            'hasil_diharapkan'  => 'Menurunkan harga ke batas wajar Rp 45.000',
            'tanggal_mulai'     => now()->toDateString(),
            'target_selesai'    => now()->addDays(2)->toDateString(),
            'pic_nama'          => 'Bambang Sudrajat',
            'pic_jabatan'       => 'Kabid Ketahanan Pangan',
            'instansi_terlibat' => ['Dinas Perdagangan', 'Perum Bulog'],
        ];

        $responseIntv = $this->actingAs($admin)->post("/intervensi/{$issue->id}", $intvData);
        $responseIntv->assertSessionHasNoErrors();

        $issue->refresh();
        $this->assertEquals('intervensi', $issue->status);

        // TAHAP 6: Admin closes issue with resolution
        $resolusiData = [
            'harga_sebelum_intervensi' => 75000,
            'harga_setelah_intervensi' => 48000,
            'status_akhir'             => 'selesai',
            'kesimpulan'               => 'Operasi pasar berhasil menstabilkan harga cabai kembali normal dalam 2 hari.',
            'catatan_akhir'            => 'Arsip berita acara penutupan nomor 800/SP/2026.',
        ];

        $responseRes = $this->actingAs($admin)->post("/resolusi/{$issue->id}", $resolusiData);
        $responseRes->assertSessionHasNoErrors();

        $issue->refresh();
        $this->assertEquals('selesai', $issue->status);

        // Verify audit logs exist
        $this->assertGreaterThanOrEqual(4, $issue->logs()->count());
    }
}
