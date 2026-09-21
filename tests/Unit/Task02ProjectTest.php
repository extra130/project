<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Task 02 Unit Tests: Projects CRUD
 */
class Task02ProjectTest extends TestCase
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

    /** @test */
    public function admin_can_create_project(): void
    {
        $response = $this->actingAs($this->admin)->post('/projects', [
            'name'        => '林園門禁',
            'description' => '林園廠區門禁系統',
            'status'      => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'name'   => '林園門禁',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function viewer_cannot_create_project(): void
    {
        $response = $this->actingAs($this->viewer)->post('/projects', [
            'name'   => '測試專案',
            'status' => 'active',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function project_name_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post('/projects', [
            'name'   => '',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function project_status_must_be_active_or_archived(): void
    {
        $response = $this->actingAs($this->admin)->post('/projects', [
            'name'   => '測試',
            'status' => 'deleted', // invalid
        ]);

        $response->assertSessionHasErrors('status');
    }

    /** @test */
    public function project_can_be_archived_not_deleted(): void
    {
        $project = Project::factory()->create(['status' => 'active']);

        $this->actingAs($this->admin)->put("/projects/{$project->id}", [
            'name'   => $project->name,
            'status' => 'archived',
        ]);

        $this->assertDatabaseHas('projects', [
            'id'     => $project->id,
            'status' => 'archived',
        ]);
        // Record should still exist (no hard delete)
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    /** @test */
    public function project_has_many_modules_relationship(): void
    {
        $project = Project::factory()->create();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $project->modules()
        );
    }

    /** @test */
    public function project_has_many_records_relationship(): void
    {
        $project = Project::factory()->create();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $project->records()
        );
    }
}
