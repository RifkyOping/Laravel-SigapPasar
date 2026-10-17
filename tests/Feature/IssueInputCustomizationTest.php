<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\MasterData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueInputCustomizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_issue_create_page_has_customized_dropdowns_and_text_input(): void
    {
        $pengawas = User::where('role', 'pengawas_siber')->first();

        $response = $this->actingAs($pengawas)->get(route('issues.create'));

        $response->assertStatus(200);

        $html = $response->getContent();

        // 1. Verify commodity dropdown options do NOT contain '(Ref:'
        $this->assertStringNotContainsString('Ref: Rp', $html);
        $this->assertStringContainsString('Beras Premium', $html);
        $this->assertStringContainsString('Gas LPG 3kg', $html);
        $this->assertStringContainsString('BBM', $html);

        // 2. Verify Kategori Isu label and 5 new options
        $this->assertStringContainsString('Kategori Isu', $html);
        $this->assertStringContainsString('Kenaikan Harga', $html);
        $this->assertStringContainsString('Kelangkaan / Stok Kosong', $html);
        $this->assertStringContainsString('Kualitas Buruk', $html);
        $this->assertStringContainsString('Masalah Distribusi', $html);
        $this->assertStringContainsString('Pungli / Spekulasi', $html);

        // 3. Verify lokasi_spesifik is a text input, NOT a select dropdown
        $this->assertMatchesRegularExpression(
            '/<input[^>]*type="text"[^>]*name="lokasi_spesifik"/',
            $html
        );
        $this->assertDoesNotMatchRegularExpression(
            '/<select[^>]*name="lokasi_spesifik"/',
            $html
        );
        $this->assertDoesNotMatchRegularExpression(
            '/<select[^>]*name="pasar_id"/',
            $html
        );
    }

    public function test_store_issue_with_new_category_and_free_text_location(): void
    {
        $pengawas = User::where('role', 'pengawas_siber')->first();
        $komoditas = MasterData::where('type', 'komoditas')->first();
        $kabupaten = MasterData::where('type', 'kabupaten')->first();

        $data = [
            'tanggal_temuan'    => now()->format('Y-m-d H:i:s'),
            'platform'          => 'TikTok',
            'link_postingan'    => 'https://tiktok.com/@warga/video/12345',
            'nama_akun'         => '@warga_mamuju',
            'jumlah_interaksi'  => 500,
            'jumlah_mention'    => 50,
            'kutipan_postingan' => 'Harga telur ayam naik drastis di toko kelontong jalan poros!',
            'komoditas_id'      => $komoditas->id,
            'kategori_isu'      => 'Kenaikan Harga',
            'kabupaten_id'      => $kabupaten->id,
            'lokasi_spesifik'   => 'Pasar Baru Mamuju - Blok B',
            'catatan_input'     => 'Temuan patroli siber pagi hari',
        ];

        $response = $this->actingAs($pengawas)->post(route('issues.store'), $data);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $issue = Issue::latest()->first();
        $this->assertEquals('Kenaikan Harga', $issue->kategori_isu);
        $this->assertEquals('Pasar Baru Mamuju - Blok B', $issue->lokasi_spesifik);

        // Verify show page displays lokasi_spesifik and kategori
        $showResponse = $this->actingAs($pengawas)->get(route('issues.show', $issue));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Pasar Baru Mamuju - Blok B');
        $showResponse->assertSee('Kenaikan Harga');
    }
}
