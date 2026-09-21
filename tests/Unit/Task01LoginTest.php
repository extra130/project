<?php

namespace Tests\Unit;

use App\Models\Module;
use App\Models\Project;
use App\Models\Record;
use App\Models\RecordFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Task 01 Unit Tests: Laravel / MySQL / Login 確認
 */
class Task01LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_model_has_role_field(): void
    {
        $user = new User([
            'name'     => 'Test',
            'email'    => 'test@test.com',
            'password' => 'hashed',
            'role'     => 'admin',
        ]);

        $this->assertEquals('admin', $user->role);
    }

    /** @test */
    public function user_role_helpers_work_correctly(): void
    {
        $admin  = new User(['role' => 'admin']);
        $editor = new User(['role' => 'editor']);
        $viewer = new User(['role' => 'viewer']);

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->isEditor());
        $this->assertTrue($admin->isViewer());

        $this->assertFalse($editor->isAdmin());
        $this->assertTrue($editor->isEditor());
        $this->assertTrue($editor->isViewer());

        $this->assertFalse($viewer->isAdmin());
        $this->assertFalse($viewer->isEditor());
        $this->assertTrue($viewer->isViewer());
    }

    /** @test */
    public function login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/records');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_access_records(): void
    {
        $user = User::factory()->create(['role' => 'viewer']);

        $response = $this->actingAs($user)->get('/records');
        $response->assertStatus(200);
    }
}
