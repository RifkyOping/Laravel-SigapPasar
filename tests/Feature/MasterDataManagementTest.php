<?php

namespace Tests\Feature;

use App\Models\MasterData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_master_data_page_displays_komoditas_and_het_hap_headers(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $response = $this->actingAs($admin)->get(route('master-data.index', ['type' => 'komoditas']));

        $response->assertStatus(200);
        $response->assertSee('Komoditas');
        $response->assertSee('HET/HAP');
        $response->assertSee('Cari');
    }

    public function test_master_data_search_filter_works(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        // Create a unique commodity
        MasterData::create([
            'type' => 'komoditas',
            'name' => 'Beras Organik Khusus',
            'code' => 'BRS-ORG-99',
            'satuan' => 'kg',
            'harga_referensi' => 20000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('master-data.index', [
            'type' => 'komoditas',
            'search' => 'Organik Khusus',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Beras Organik Khusus');
        $response->assertSee('BRS-ORG-99');
    }

    public function test_master_data_status_filter_works(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        MasterData::create([
            'type' => 'komoditas',
            'name' => 'Komoditas Nonaktif Percobaan',
            'code' => 'KOM-NON-01',
            'satuan' => 'kg',
            'harga_referensi' => 15000,
            'is_active' => false,
        ]);

        // Filter active only
        $activeResponse = $this->actingAs($admin)->get(route('master-data.index', [
            'type' => 'komoditas',
            'status' => 'active',
            'search' => 'Komoditas Nonaktif Percobaan',
        ]));
        $activeResponse->assertStatus(200);
        $activeResponse->assertDontSee('KOM-NON-01');

        // Filter inactive only
        $inactiveResponse = $this->actingAs($admin)->get(route('master-data.index', [
            'type' => 'komoditas',
            'status' => 'inactive',
            'search' => 'Komoditas Nonaktif Percobaan',
        ]));
        $inactiveResponse->assertStatus(200);
        $inactiveResponse->assertSee('Komoditas Nonaktif Percobaan');
    }

    public function test_create_and_edit_forms_display_het_hap_label(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $komoditas = MasterData::where('type', 'komoditas')->first();

        $createResponse = $this->actingAs($admin)->get(route('master-data.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('HET/HAP (Rp)');

        $editResponse = $this->actingAs($admin)->get(route('master-data.edit', $komoditas));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('HET/HAP (Rp)');
    }
}
