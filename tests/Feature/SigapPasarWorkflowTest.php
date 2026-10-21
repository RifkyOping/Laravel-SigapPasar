<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MasterData;
use App\Models\Issue;
use App\Models\FieldAssignment;
use App\Models\Intervention;
use App\Models\Resolution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_complete_5_stages_workflow(): void
    {
        // 1. Setup users and master data
        $pengawas = User::where('role', 'pengawas_siber')->first();
        $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
        $petugas = User::where('role', 'petugas_lapangan')->first();
        $komoditas = MasterData::where('type', 'komoditas')->first();
        $kabupaten = MasterData::where('type', 'kabupaten')->first();

        $this->assertNotNull($pengawas);
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

        // TAHAP 2: Admin assigns field officer directly
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

        // TAHAP 3: Petugas Lapangan check-in GPS & submits field report
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

        // TAHAP 4: Admin launches intervention
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

        // TAHAP 5: Admin closes issue with resolution
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

    public function test_admin_can_create_intervention_with_optional_pdf_or_word_document(): void
    {
        Storage::fake('public');

        $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
        $komoditas = MasterData::where('type', 'komoditas')->first();
        $kabupaten = MasterData::where('type', 'kabupaten')->first();

        $issue = Issue::create([
            'kode_isu'          => 'ISU-' . date('Ymd') . '-999',
            'input_by'          => $admin->id,
            'tanggal_temuan'    => now(),
            'platform'          => 'Twitter/X',
            'link_postingan'    => 'https://x.com/sulbar/status/1',
            'nama_akun'         => '@sulbar',
            'jumlah_interaksi'  => 1000,
            'jumlah_mention'    => 50,
            'kutipan_postingan' => 'Harga beras melonjak',
            'komoditas_id'      => $komoditas->id,
            'kategori_isu'      => 'lonjakan_harga',
            'kabupaten_id'      => $kabupaten->id,
            'status'            => 'di_lapangan',
            'virality_score'    => 50,
            'prioritas'         => 'tinggi',
        ]);

        // 1. Cek halaman form create intervensi menampilkan input dokumen pendukung
        $formResponse = $this->actingAs($admin)->get("/intervensi/buat/{$issue->id}");
        $formResponse->assertStatus(200);
        $formResponse->assertSee('name="dokumen_pendukung"', false);

        // 2. Submit form dengan file PDF
        $fakePdf = UploadedFile::fake()->create('surat_tugas_operasi_pasar.pdf', 300, 'application/pdf');

        $data = [
            'jenis_intervensi'  => 'operasi_pasar',
            'nama_intervensi'   => 'Operasi Beras SPHP Pasar Baru',
            'deskripsi'         => 'Penyaluran beras medium bersama Bulog sebanyak 5 ton.',
            'hasil_diharapkan'  => 'Harga kembali ke HET Rp 12.500/kg',
            'tanggal_mulai'     => now()->toDateString(),
            'target_selesai'    => now()->addDays(2)->toDateString(),
            'pic_nama'          => 'Andi Irwan',
            'pic_jabatan'       => 'Staf Pengendalian Pasar',
            'dokumen_pendukung' => $fakePdf,
        ];

        $response = $this->actingAs($admin)->post("/intervensi/{$issue->id}", $data);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('issues.show', $issue));

        $intervention = Intervention::where('nama_intervensi', 'Operasi Beras SPHP Pasar Baru')->first();
        $this->assertNotNull($intervention);
        $this->assertNotNull($intervention->file_dokumen);
        $this->assertEquals('surat_tugas_operasi_pasar.pdf', $intervention->nama_dokumen);

        // Verifikasi file tersimpan di storage public
        Storage::disk('public')->assertExists($intervention->file_dokumen);

        // Verifikasi record evidence juga tercatat otomatis
        $evidence = $issue->evidences()->where('tahap', 'intervensi')->first();
        $this->assertNotNull($evidence);
        $this->assertEquals('surat_tugas_operasi_pasar.pdf', $evidence->file_name);

        // 3. Verifikasi halaman detail menampilkan link dokumen
        $detailResponse = $this->actingAs($admin)->get(route('issues.show', $issue));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('surat_tugas_operasi_pasar.pdf');
    }
}
