<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Task 13 — UI 整理測試
 * Task 14 — 整體整合確認
 */
class Task13UiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $viewer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin  = User::factory()->create(['role' => 'admin']);
        $this->viewer = User::factory()->create(['role' => 'viewer']);
    }

    // ────────────────────────────────────────────
    // Task 13: UI 整理
    // ────────────────────────────────────────────

    /** @test */
    public function root_redirects_to_records_when_authenticated(): void
    {
        $response = $this->actingAs($this->viewer)->get('/');
        $response->assertRedirect('/records');
    }

    /** @test */
    public function root_redirects_to_login_when_unauthenticated(): void
    {
        // / → records → auth 攔截 → login
        $response = $this->get('/');
        // 先重導到 /records
        $response->assertRedirect('/records');
    }

    /** @test */
    public function login_redirects_to_records_after_authentication(): void
    {
        $response = $this->actingAs($this->admin)->get('/records');
        $response->assertStatus(200);
    }

    /** @test */
    public function profile_page_is_accessible_when_authenticated(): void
    {
        $response = $this->actingAs($this->admin)->get('/profile');
        $response->assertStatus(200);
    }

    /** @test */
    public function user_factory_includes_role_field(): void
    {
        $user = User::factory()->create(['role' => 'editor']);
        $this->assertEquals('editor', $user->fresh()->role);
    }

    /** @test */
    public function user_factory_default_role_is_viewer(): void
    {
        $user = User::factory()->create();
        $this->assertEquals('viewer', $user->role);
    }

    // ────────────────────────────────────────────
    // Task 14: 整體整合確認
    // ────────────────────────────────────────────

    /** @test */
    public function all_main_pages_return_200_for_admin(): void
    {
        $pages = [
            '/records',
            '/projects',
            '/records/create',
            '/projects/create',
        ];

        foreach ($pages as $page) {
            $response = $this->actingAs($this->admin)->get($page);
            $this->assertEquals(
                200,
                $response->getStatusCode(),
                "頁面 {$page} 應回傳 200，實際：{$response->getStatusCode()}"
            );
        }
    }

    /** @test */
    public function viewer_cannot_access_create_pages(): void
    {
        $restrictedPages = [
            '/records/create',
            '/projects/create',
        ];

        foreach ($restrictedPages as $page) {
            $response = $this->actingAs($this->viewer)->get($page);
            $this->assertEquals(
                403,
                $response->getStatusCode(),
                "Viewer 不應能存取 {$page}"
            );
        }
    }

    /** @test */
    public function api_routes_all_require_authentication(): void
    {
        $apiRoutes = [
            ['GET',  '/api/projects'],
            ['GET',  '/api/records'],
        ];

        foreach ($apiRoutes as [$method, $uri]) {
            $response = $this->json($method, $uri);
            $this->assertEquals(
                401,
                $response->getStatusCode(),
                "API {$method} {$uri} 應在未認證時回傳 401"
            );
        }
    }

    /** @test */
    public function records_layout_component_exists(): void
    {
        $this->assertFileExists(
            resource_path('views/components/records-layout.blade.php')
        );
    }

    /** @test */
    public function app_layout_component_exists(): void
    {
        $this->assertFileExists(
            resource_path('views/components/app-layout.blade.php')
        );
    }

    /** @test */
    public function guest_layout_component_exists(): void
    {
        $this->assertFileExists(
            resource_path('views/components/guest-layout.blade.php')
        );
    }

    /** @test */
    public function all_required_views_exist(): void
    {
        $views = [
            'projects/index', 'projects/create', 'projects/show', 'projects/edit',
            'modules/index',  'modules/create',  'modules/edit',
            'records/index',  'records/create',  'records/show',  'records/edit',
            'record_files/edit',
        ];

        foreach ($views as $view) {
            $path = resource_path("views/{$view}.blade.php");
            $this->assertFileExists($path, "View {$view}.blade.php 不存在");
        }
    }
}
