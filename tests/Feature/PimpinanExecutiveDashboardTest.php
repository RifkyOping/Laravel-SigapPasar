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

        $response->assertStatus(200);
        $response->assertSeeText('Ringkasan Eksekutif Perkembangan Isu & Harga Pasar');
        $response->assertSeeText('Peta Sebaran Titik Isu & Status Kerawanan 6 Kabupaten');
        $response->assertSeeText('Perkembangan & Deviasi Harga Komoditas Pokok vs HET');
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

    public function test_pimpinan_can_filter_dashboard_by_periods(): void
    {
        $pimpinan = User::where('role', 'pimpinan')->first();

        foreach (['hari', 'minggu', 'bulan', 'tahun'] as $periode) {
            $response = $this->actingAs($pimpinan)->get("/dashboard?periode={$periode}");
            $response->assertStatus(200);
            $response->assertSee('Ringkasan Eksekutif');
        }
    }

    public function test_pimpinan_is_redirected_away_from_technical_issue_management(): void
    {
        $pimpinan = User::where('role', 'pimpinan')->first();

        $response = $this->actingAs($pimpinan)->get('/isu');
        $response->assertRedirect('/dashboard');
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
}
