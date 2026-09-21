<?php

namespace Tests\Unit;

use App\Models\Module;
use App\Models\Project;
use App\Models\Record;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Task 04 Unit Tests: Records CRUD
 * Task 05 Unit Tests: Record Search
 */
class Task04RecordTest extends TestCase
{
    use RefreshDatabase;

    private User    $admin;
    private User    $viewer;
    private Project $project;
    private Module  $module;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin   = User::factory()->create(['role' => 'admin']);
        $this->viewer  = User::factory()->create(['role' => 'viewer']);
        $this->project = Project::factory()->create();
        $this->module  = Module::factory()->create(['project_id' => $this->project->id]);
    }

    /** @test */
    public function editor_can_create_record(): void
    {
        $response = $this->actingAs($this->admin)->post('/records', [
            'project_id' => $this->project->id,
            'module_id'  => $this->module->id,
            'type'       => 'development',
            'title'      => 'P2 相容性修正',
            'content'    => '修改 DoorTrait.php 的 P2 判斷',
            'source'     => 'manual',
            'git_branch' => 'develop',
            'git_commit' => 'abc123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('records', [
            'title'      => 'P2 相容性修正',
            'type'       => 'development',
            'project_id' => $this->project->id,
        ]);
    }

    /** @test */
    public function viewer_cannot_create_record(): void
    {
        $response = $this->actingAs($this->viewer)->post('/records', [
            'project_id' => $this->project->id,
            'type'       => 'note',
            'title'      => '測試',
            'content'    => '...',
            'source'     => 'manual',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function record_type_must_be_valid(): void
    {
        $response = $this->actingAs($this->admin)->post('/records', [
            'project_id' => $this->project->id,
            'type'       => 'invalid_type',
            'title'      => '測試',
            'content'    => '...',
            'source'     => 'manual',
        ]);

        $response->assertSessionHasErrors('type');
    }

    /** @test */
    public function record_source_must_be_valid(): void
    {
        $response = $this->actingAs($this->admin)->post('/records', [
            'project_id' => $this->project->id,
            'type'       => 'note',
            'title'      => '測試',
            'content'    => '...',
            'source'     => 'invalid_source',
        ]);

        $response->assertSessionHasErrors('source');
    }

    /** @test */
    public function record_constants_are_correct(): void
    {
        $this->assertEquals(
            ['development', 'test', 'issue', 'note'],
            Record::TYPES
        );

        $this->assertEquals(
            ['manual', 'codex', 'agent', 'api'],
            Record::SOURCES
        );
    }

    /** @test */
    public function record_relationships_are_correct(): void
    {
        $record = Record::factory()->create([
            'project_id' => $this->project->id,
            'module_id'  => $this->module->id,
        ]);

        $this->assertEquals($this->project->id, $record->project->id);
        $this->assertEquals($this->module->id, $record->module->id);
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $record->files()
        );
    }

    // ========== Task 05: Search Tests ==========

    /** @test */
    public function search_by_keyword_in_title(): void
    {
        Record::factory()->create([
            'project_id' => $this->project->id,
            'title'      => '屆齡人員處理',
            'type'       => 'test',
            'source'     => 'manual',
        ]);
        Record::factory()->create([
            'project_id' => $this->project->id,
            'title'      => '無關紀錄',
            'type'       => 'note',
            'source'     => 'manual',
        ]);

        $response = $this->actingAs($this->viewer)->get('/records?keyword=屆齡');
        $response->assertStatus(200);
        $response->assertSee('屆齡人員處理');
        $response->assertDontSee('無關紀錄');
    }

    /** @test */
    public function search_filter_by_type(): void
    {
        Record::factory()->create([
            'project_id' => $this->project->id,
            'title'      => '開發紀錄A',
            'type'       => 'development',
            'source'     => 'manual',
        ]);
        Record::factory()->create([
            'project_id' => $this->project->id,
            'title'      => '測試紀錄B',
            'type'       => 'test',
            'source'     => 'manual',
        ]);

        $response = $this->actingAs($this->viewer)->get('/records?type=development');
        $response->assertStatus(200);
        $response->assertSee('開發紀錄A');
        $response->assertDontSee('測試紀錄B');
    }

    /** @test */
    public function search_filter_by_project_id(): void
    {
        $other = Project::factory()->create();

        Record::factory()->create([
            'project_id' => $this->project->id,
            'title'      => '本專案紀錄',
            'type'       => 'note',
            'source'     => 'manual',
        ]);
        Record::factory()->create([
            'project_id' => $other->id,
            'title'      => '他專案紀錄',
            'type'       => 'note',
            'source'     => 'manual',
        ]);

        $response = $this->actingAs($this->viewer)->get("/records?project_id={$this->project->id}");
        $response->assertStatus(200);
        $response->assertSee('本專案紀錄');
        $response->assertDontSee('他專案紀錄');
    }
}
