<?php

namespace Tests\Unit;

use App\Models\Module;
use App\Models\Project;
use App\Models\Record;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Task 03 Unit Tests: Modules CRUD
 */
class Task03ModuleTest extends TestCase
{
    use RefreshDatabase;

    private User    $admin;
    private User    $viewer;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin   = User::factory()->create(['role' => 'admin']);
        $this->viewer  = User::factory()->create(['role' => 'viewer']);
        $this->project = Project::factory()->create();
    }

    /** @test */
    public function admin_can_create_module(): void
    {
        $response = $this->actingAs($this->admin)
            ->post("/projects/{$this->project->id}/modules", [
                'name'        => '門禁入廠',
                'description' => '入廠流程',
                'sort_order'  => 1,
                'status'      => 'active',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('modules', [
            'name'       => '門禁入廠',
            'project_id' => $this->project->id,
        ]);
    }

    /** @test */
    public function viewer_cannot_create_module(): void
    {
        $response = $this->actingAs($this->viewer)
            ->post("/projects/{$this->project->id}/modules", [
                'name'   => '模組',
                'status' => 'active',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function module_name_is_required(): void
    {
        $response = $this->actingAs($this->admin)
            ->post("/projects/{$this->project->id}/modules", [
                'name'   => '',
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function module_belongs_to_project_relationship(): void
    {
        $module = Module::factory()->create(['project_id' => $this->project->id]);

        $this->assertEquals($this->project->id, $module->project->id);
    }

    /** @test */
    public function module_has_many_records_relationship(): void
    {
        $module = Module::factory()->create(['project_id' => $this->project->id]);

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $module->records()
        );
    }

    /** @test */
    public function modules_are_ordered_by_sort_order(): void
    {
        Module::factory()->create(['project_id' => $this->project->id, 'sort_order' => 2, 'name' => 'B']);
        Module::factory()->create(['project_id' => $this->project->id, 'sort_order' => 1, 'name' => 'A']);
        Module::factory()->create(['project_id' => $this->project->id, 'sort_order' => 3, 'name' => 'C']);

        $modules = $this->project->modules()->pluck('name')->toArray();

        $this->assertEquals(['A', 'B', 'C'], $modules);
    }
}
