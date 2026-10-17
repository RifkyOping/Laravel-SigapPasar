<?php

namespace Tests\Feature;

use App\Models\FieldAssignment;
use App\Models\Intervention;
use App\Models\Issue;
use App\Models\MasterData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntervensiQueueWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_intervensi_index_shows_antrian_and_berjalan_tabs(): void
    {
        $admin = User::where('role', 'admin')->first();
        $pengawas = User::where('role', 'pengawas_siber')->first();
        $petugas = User::where('role', 'petugas_lapangan')->first();
        $komoditas = MasterData::where('type', 'komoditas')->first();
        $kabupaten = MasterData::where('type', 'kabupaten')->first();

        // Create an issue that is di_lapangan with completed field report
        $issue = Issue::create([
            'tanggal_temuan'    => now(),
            'platform'          => 'TikTok',
            'link_postingan'    => 'https://tiktok.com/@tes',
            'nama_akun'         => '@tes',
            'jumlah_interaksi'  => 500,
            'jumlah_mention'    => 20,
            'kutipan_postingan' => 'Harga beras melonjak tinggi di pasar baru!',
            'komoditas_id'      => $komoditas->id,
            'kategori_isu'      => 'Kenaikan Harga',
            'kabupaten_id'      => $kabupaten->id,
            'lokasi_spesifik'   => 'Pasar Baru Mamuju',
            'input_by'          => $pengawas->id,
            'status'            => 'di_lapangan',
            'prioritas'         => 'tinggi',
        ]);

        FieldAssignment::create([
            'issue_id'          => $issue->id,
            'petugas_id'        => $petugas->id,
            'assigned_by'       => $admin->id,
            'tanggal_penugasan' => now(),
            'tanggal_kunjungan' => now(),
            'status'            => 'selesai',
            'harga_aktual'      => 18000,
            'catatan_temuan'    => 'Stok beras menipis dan harga pedagang naik di atas HET.',
        ]);

        // 1. Visit /intervensi as admin
        $response = $this->actingAs($admin)->get(route('intervensi.index', ['tab' => 'antrian']));
        $response->assertStatus(200);

        // Verify queue tab displays the issue and price info
        $response->assertSee('Antrian Menunggu Intervensi');
        $response->assertSee($issue->kode_isu);
        $response->assertSee('18.000');
        $response->assertSee('Buat Intervensi');

        // 2. Now create intervention for this issue
        $dataIntervensi = [
            'jenis_intervensi'  => 'operasi_pasar',
            'nama_intervensi'   => 'Operasi Beras SPHP Pasar Baru',
            'deskripsi'         => 'Penyaluran 5 ton beras SPHP bersama Bulog.',
            'tanggal_mulai'     => now()->toDateString(),
            'target_selesai'    => now()->addDays(2)->toDateString(),
            'pic_nama'          => 'Admin Diskoperindag',
        ];

        $storeResponse = $this->actingAs($admin)->post(route('intervensi.store', $issue), $dataIntervensi);
        $storeResponse->assertRedirect();

        // 3. Verify issue is now under Intervensi Berjalan
        $responseBerjalan = $this->actingAs($admin)->get(route('intervensi.index', ['tab' => 'berjalan']));
        $responseBerjalan->assertStatus(200);
        $responseBerjalan->assertSee('Operasi Beras SPHP Pasar Baru');
        $responseBerjalan->assertSee($issue->kode_isu);
    }
}
