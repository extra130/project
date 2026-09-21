<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Record;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Task 09-12 Unit Tests: Codex / Agent API
 * - Task 09: Token auth
 * - Task 10: Read Records API
 * - Task 11: Create Record API
 * - Task 12: Upload Attachment API
 */
class Task09ApiTest extends TestCase
{
    use RefreshDatabase;

    private User    $apiUser;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiUser  = User::factory()->create(['role' => 'editor']);
        $this->project  = Project::factory()->create(['status' => 'active']);
    }

    // ========== Task 09: Token ==========

    /** @test */
    public function api_requires_sanctum_token(): void
    {
        $response = $this->getJson('/api/projects');
        $response->assertStatus(401);
    }

    /** @test */
    public function api_returns_401_without_token(): void
    {
        $response = $this->getJson('/api/records');
        $response->assertStatus(401);
    }

    // ========== Task 10: Read ==========

    /** @test */
    public function authenticated_api_can_list_projects(): void
    {
        Sanctum::actingAs($this->apiUser);

        $response = $this->getJson('/api/projects');
        $response->assertStatus(200)
                 ->assertJsonStructure([['id', 'name', 'status']]);
    }

    /** @test */
    public function authenticated_api_can_list_records(): void
    {
        Sanctum::actingAs($this->apiUser);

        Record::factory()->create([
            'project_id' => $this->project->id,
            'type'       => 'test',
            'source'     => 'codex',
        ]);

        $response = $this->getJson('/api/records');
        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    /** @test */
    public function api_can_filter_records_by_project_id(): void
    {
        Sanctum::actingAs($this->apiUser);

        $other = Project::factory()->create();
        Record::factory()->create(['project_id' => $this->project->id, 'title' => '本專案', 'type' => 'note', 'source' => 'manual']);
        Record::factory()->create(['project_id' => $other->id, 'title' => '他專案', 'type' => 'note', 'source' => 'manual']);

        $response = $this->getJson("/api/records?project_id={$this->project->id}");
        $response->assertStatus(200);

        $titles = collect($response->json('data'))->pluck('title')->toArray();
        $this->assertContains('本專案', $titles);
        $this->assertNotContains('他專案', $titles);
    }

    /** @test */
    public function api_can_search_records_by_keyword(): void
    {
        Sanctum::actingAs($this->apiUser);

        Record::factory()->create([
            'project_id' => $this->project->id,
            'title'      => 'P2 相容性修正',
            'type'       => 'development',
            'source'     => 'codex',
        ]);

        $response = $this->getJson('/api/records?keyword=P2');
        $response->assertStatus(200);

        $titles = collect($response->json('data'))->pluck('title')->toArray();
        $this->assertContains('P2 相容性修正', $titles);
    }

    // ========== Task 11: Create ==========

    /** @test */
    public function api_can_create_development_record(): void
    {
        Sanctum::actingAs($this->apiUser);

        $response = $this->postJson('/api/records', [
            'project_id' => $this->project->id,
            'type'       => 'development',
            'title'      => 'Codex 寫入測試',
            'content'    => '修改原因：...  修改內容：...',
            'source'     => 'codex',
            'git_branch' => 'develop',
            'git_commit' => 'abc123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Codex 寫入測試']);

        $this->assertDatabaseHas('records', [
            'title'  => 'Codex 寫入測試',
            'source' => 'codex',
            'type'   => 'development',
        ]);
    }

    /** @test */
    public function api_validates_record_type(): void
    {
        Sanctum::actingAs($this->apiUser);

        $response = $this->postJson('/api/records', [
            'project_id' => $this->project->id,
            'type'       => 'invalid',
            'title'      => 'Test',
            'content'    => '...',
            'source'     => 'codex',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('type');
    }

    // ========== Task 12: Upload Attachment ==========

    /** @test */
    public function api_can_upload_attachment(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');
        Sanctum::actingAs($this->apiUser);

        $record = Record::factory()->create(['project_id' => $this->project->id]);
        $file   = \Illuminate\Http\UploadedFile::fake()->create('evidence.pdf', 50, 'application/pdf');

        $response = $this->postJson("/api/records/{$record->id}/files", [
            'files' => [$file],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('record_files', [
            'record_id'     => $record->id,
            'original_name' => 'evidence.pdf',
        ]);
    }
}
