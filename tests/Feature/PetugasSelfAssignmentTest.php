<?php

namespace Tests\Feature;

use App\Models\FieldAssignment;
use App\Models\Issue;
use App\Models\MasterData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasSelfAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\MasterDataSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);
    }
    public function test_petugas_lapangan_can_view_all_inputted_issues_in_laporan_page(): void
    {
        $petugas = User::where('role', 'petugas_lapangan')->first();
        $komoditas = MasterData::where('type', 'komoditas')->first();
        $kabupaten = MasterData::where('type', 'kabupaten')->first();

        // Create an issue that is inputted ('baru')
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
            'input_by'         => $petugas->id,
        ]);

        $response = $this->actingAs($petugas)->get(route('laporan.index'));

        $response->assertStatus(200);
        $response->assertSeeText($issue->kode_isu);
        $response->assertSeeText('Pilih & Kerjakan');
        $response->assertSeeText('Tugas Belum Selesai');
        $response->assertSeeText('Tugas Sudah Selesai');

        // Verify column title is 'Komoditas & Lokasi' and no leaked &bull;
        $html = $response->getContent();
        $this->assertStringContainsString('Komoditas & Lokasi', $html);
        $this->assertStringNotContainsString('&bull;', $html);
        $this->assertStringContainsString('•', $html);
    }

    public function test_petugas_lapangan_can_pick_issue_and_complete_workflow(): void
    {
        $petugas = User::where('role', 'petugas_lapangan')->first();
        $komoditas = MasterData::where('type', 'komoditas')->first();
        $kabupaten = MasterData::where('type', 'kabupaten')->first();

        $issue = Issue::create([
            'tanggal_temuan'   => now(),
            'platform'         => 'Facebook',
            'link_postingan'   => 'https://facebook.com/warga/posts/998877',
            'nama_akun'        => 'Komunitas Pasar Majene',
            'jumlah_interaksi' => 80,
            'jumlah_mention'   => 5,
            'kutipan_postingan'=> 'Cabai rawit tembus 100 ribu',
            'komoditas_id'     => $komoditas->id,
            'kategori_isu'     => 'Kenaikan Harga',
            'kabupaten_id'     => $kabupaten->id,
            'lokasi_spesifik'  => 'Pasar Tradisional',
            'status'           => 'baru',
            'prioritas'        => 'sedang',
            'input_by'         => $petugas->id,
        ]);

        // 1. Petugas picks the issue
        $pickResponse = $this->actingAs($petugas)->post(route('laporan.ambil', $issue));
        $pickResponse->assertSessionHasNoErrors();

        $assignment = FieldAssignment::where('issue_id', $issue->id)
            ->where('petugas_id', $petugas->id)
            ->first();

        $this->assertNotNull($assignment);
        $this->assertEquals('sedang_dikerjakan', $assignment->status);
        $pickResponse->assertRedirect(route('laporan.show', $assignment));

        $issue->refresh();
        $this->assertEquals('di_lapangan', $issue->status);

        // 2. Check-in GPS
        $checkinResponse = $this->actingAs($petugas)->post(route('laporan.checkin', $assignment), [
            'latitude'  => -3.5412,
            'longitude' => 118.9721,
        ]);
        $checkinResponse->assertSessionHasNoErrors();

        // 3. Submit report
        $submitResponse = $this->actingAs($petugas)->post(route('laporan.submit', $assignment), [
            'harga_aktual'      => 98000,
            'tanggal_kunjungan' => now()->toDateString(),
            'catatan_temuan'    => 'Hasil survei riil di lapak pedagang mendapati pasokan terbatas dari distributor.',
        ]);

        $submitResponse->assertSessionHasNoErrors();
        $submitResponse->assertRedirect(route('laporan.index', ['tab' => 'selesai']));

        $assignment->refresh();
        $this->assertEquals('selesai', $assignment->status);

        // 4. View completed tab
        $completedTabResponse = $this->actingAs($petugas)->get(route('laporan.index', ['tab' => 'selesai']));
        $completedTabResponse->assertStatus(200);
        $completedTabResponse->assertSeeText($issue->kode_isu);
        $completedTabResponse->assertSeeText('98.000');
        $completedTabResponse->assertSeeText('Selesai Dilaporkan');
    }

    public function test_petugas_lapangan_can_verify_issue_online_with_contact_or_social_media(): void
    {
        $petugas = User::where('role', 'petugas_lapangan')->first();
        $komoditas = MasterData::where('type', 'komoditas')->first();
        $kabupaten = MasterData::where('type', 'kabupaten')->first();

        $issue = Issue::create([
            'tanggal_temuan'   => now(),
            'platform'         => 'Instagram',
            'link_postingan'   => 'https://instagram.com/p/abcdef123',
            'nama_akun'        => '@infomamuju',
            'jumlah_interaksi' => 120,
            'jumlah_mention'   => 12,
            'kutipan_postingan'=> 'Minyak goreng langka dan mahal di toko grosir',
            'komoditas_id'     => $komoditas->id,
            'kategori_isu'     => 'Kelangkaan',
            'kabupaten_id'     => $kabupaten->id,
            'lokasi_spesifik'  => 'Toko Grosir Maju',
            'status'           => 'baru',
            'prioritas'        => 'kritis',
            'input_by'         => $petugas->id,
        ]);

        $this->actingAs($petugas)->post(route('laporan.ambil', $issue));

        $assignment = FieldAssignment::where('issue_id', $issue->id)->first();
        $this->assertNotNull($assignment);

        // Validasi gagal jika online tapi kontak kosong
        $failCheckin = $this->actingAs($petugas)->post(route('laporan.checkin', $assignment), [
            'metode_verifikasi' => 'online',
            'no_telepon_sumber' => '',
            'sosmed_sumber'     => '',
        ]);
        $failCheckin->assertSessionHasErrors(['no_telepon_sumber']);

        // Check-in / verifikasi online berhasil
        $successCheckin = $this->actingAs($petugas)->post(route('laporan.checkin', $assignment), [
            'metode_verifikasi' => 'online',
            'no_telepon_sumber' => '081234567890',
            'sosmed_sumber'     => '@grosir_maju_mamuju',
        ]);
        $successCheckin->assertSessionHasNoErrors();

        $assignment->refresh();
        $this->assertEquals('online', $assignment->metode_verifikasi);
        $this->assertEquals('081234567890', $assignment->no_telepon_sumber);
        $this->assertEquals('@grosir_maju_mamuju', $assignment->sosmed_sumber);
        $this->assertNull($assignment->latitude);
        $this->assertNull($assignment->longitude);
        $this->assertNotNull($assignment->checkin_at);

        // Submit laporan hasil cek online
        $submitResponse = $this->actingAs($petugas)->post(route('laporan.submit', $assignment), [
            'harga_aktual'      => 17000,
            'tanggal_kunjungan' => now()->toDateString(),
            'catatan_temuan'    => 'Klarifikasi via telepon dan pesan direct message mengonfirmasi pasokan berkurang karena kendala ekspedisi.',
        ]);
        $submitResponse->assertSessionHasNoErrors();

        $assignment->refresh();
        $this->assertEquals('selesai', $assignment->status);

        // Cek halaman detail isu menampilkan info verifikasi online
        $issueResponse = $this->actingAs($petugas)->get(route('issues.show', $issue));
        $issueResponse->assertStatus(200);
        $issueResponse->assertSeeText('Online (Klarifikasi Daring)');
        $issueResponse->assertSeeText('081234567890');
        $issueResponse->assertSeeText('@grosir_maju_mamuju');
    }
}
