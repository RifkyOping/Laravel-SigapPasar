<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarActiveStateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_input_isu_baru_is_solely_active_on_create_page(): void
    {
        $user = User::where('role', 'pengawas_siber')->first();

        $response = $this->actingAs($user)->get(route('issues.create'));

        $response->assertStatus(200);

        $html = $response->getContent();

        // Check Input Isu Baru link has active
        $this->assertMatchesRegularExpression(
            '/<a[^>]*href="[^"]*\/isu\/buat"[^>]*class="[^"]*active[^"]*"[^>]*>/',
            $html,
            'Input Isu Baru should have active class'
        );

        // Check Semua Isu link does NOT have active
        $this->assertDoesNotMatchRegularExpression(
            '/<a[^>]*href="[^"]*\/isu"[^>]*class="[^"]*active[^"]*"[^>]*>/',
            $html,
            'Semua Isu should not have active class on create page'
        );
    }

    public function test_semua_isu_is_solely_active_on_index_page(): void
    {
        $user = User::where('role', 'pengawas_siber')->first();

        $response = $this->actingAs($user)->get(route('issues.index'));

        $response->assertStatus(200);

        $html = $response->getContent();

        // Check Semua Isu link has active
        $this->assertMatchesRegularExpression(
            '/<a[^>]*href="[^"]*\/isu"[^>]*class="[^"]*active[^"]*"[^>]*>/',
            $html,
            'Semua Isu should have active class'
        );

        // Check Input Isu Baru link does NOT have active
        $this->assertDoesNotMatchRegularExpression(
            '/<a[^>]*href="[^"]*\/isu\/buat"[^>]*class="[^"]*active[^"]*"[^>]*>/',
            $html,
            'Input Isu Baru should not have active class on index page'
        );
    }

    public function test_dashboard_is_solely_active_on_dashboard_page(): void
    {
        $user = User::where('role', 'pengawas_siber')->first();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);

        $html = $response->getContent();

        // Check Dashboard link has active
        $this->assertMatchesRegularExpression(
            '/<a[^>]*href="[^"]*\/dashboard"[^>]*class="[^"]*active[^"]*"[^>]*>/',
            $html,
            'Dashboard should have active class'
        );

        // Neither issue link should have active
        $this->assertDoesNotMatchRegularExpression(
            '/<a[^>]*href="[^"]*\/isu"[^>]*class="[^"]*active[^"]*"[^>]*>/',
            $html
        );
        $this->assertDoesNotMatchRegularExpression(
            '/<a[^>]*href="[^"]*\/isu\/buat"[^>]*class="[^"]*active[^"]*"[^>]*>/',
            $html
        );
    }
}
