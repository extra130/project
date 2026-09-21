<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Record;
use App\Models\RecordFile;
use App\Models\User;
use App\Services\RecordFileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Task 06 Unit Tests: record_files 多附件上傳
 * Task 07 Unit Tests: 附件重新命名 / 備註 / 排序
 * Task 08 Unit Tests: 附件預覽 / 下載 / 刪除
 */
class Task06FileTest extends TestCase
{
    use RefreshDatabase;

    private User           $admin;
    private User           $viewer;
    private Record         $record;
    private RecordFileService $fileService;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->admin  = User::factory()->create(['role' => 'admin']);
        $this->viewer = User::factory()->create(['role' => 'viewer']);

        $project      = Project::factory()->create();
        $this->record = Record::factory()->create(['project_id' => $project->id]);
        $this->fileService = app(RecordFileService::class);
    }

    // ========== Task 06: Upload ==========

    /** @test */
    public function editor_can_upload_file_to_record(): void
    {
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->post("/records/{$this->record->id}/files", [
                'files' => [$file],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('record_files', [
            'record_id'     => $this->record->id,
            'original_name' => 'test.pdf',
            'extension'     => 'pdf',
        ]);
    }

    /** @test */
    public function file_is_stored_with_uuid_path_not_original_name(): void
    {
        $file = UploadedFile::fake()->create('my_file.txt', 10, 'text/plain');

        $saved = $this->fileService->storeFiles($this->record, [$file], $this->admin->id);

        $this->assertCount(1, $saved);
        $this->assertStringNotContainsString('my_file', $saved[0]->storage_path);
        $this->assertStringStartsWith('records/', $saved[0]->storage_path);
    }

    /** @test */
    public function multiple_files_can_be_uploaded_at_once(): void
    {
        $files = [
            UploadedFile::fake()->create('a.jpg', 50, 'image/jpeg'),
            UploadedFile::fake()->create('b.jpg', 50, 'image/jpeg'),
            UploadedFile::fake()->create('c.xlsx', 80, 'application/vnd.ms-excel'),
        ];

        $saved = $this->fileService->storeFiles($this->record, $files, $this->admin->id);

        $this->assertCount(3, $saved);
        $this->assertDatabaseCount('record_files', 3);
    }

    /** @test */
    public function viewer_cannot_upload_file(): void
    {
        $file = UploadedFile::fake()->create('test.pdf', 10);

        $response = $this->actingAs($this->viewer)
            ->post("/records/{$this->record->id}/files", [
                'files' => [$file],
            ]);

        $response->assertStatus(403);
    }

    // ========== Task 07: Rename / Note / Sort ==========

    /** @test */
    public function editor_can_update_display_name_and_note(): void
    {
        $recordFile = RecordFile::factory()->create([
            'record_id'    => $this->record->id,
            'display_name' => 'old-name',
            'note'         => null,
            'sort_order'   => 0,
        ]);

        $response = $this->actingAs($this->admin)
            ->put("/record-files/{$recordFile->id}", [
                'display_name' => '林園門禁入廠流程圖',
                'note'         => '主管確認流程時使用',
                'sort_order'   => 2,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('record_files', [
            'id'           => $recordFile->id,
            'display_name' => '林園門禁入廠流程圖',
            'note'         => '主管確認流程時使用',
            'sort_order'   => 2,
        ]);
    }

    /** @test */
    public function original_name_cannot_be_modified_via_update(): void
    {
        $recordFile = RecordFile::factory()->create([
            'record_id'     => $this->record->id,
            'original_name' => 'S__13656072.jpg',
            'display_name'  => 'old',
        ]);

        // Attempt to sneak original_name in the update
        $this->actingAs($this->admin)
            ->put("/record-files/{$recordFile->id}", [
                'display_name'  => 'new-name',
                'original_name' => 'hacked.jpg', // should be ignored
            ]);

        // original_name must remain unchanged
        $this->assertDatabaseHas('record_files', [
            'id'            => $recordFile->id,
            'original_name' => 'S__13656072.jpg',
        ]);
    }

    // ========== Task 08: Download / Delete ==========

    /** @test */
    public function viewer_can_download_file(): void
    {
        $file = UploadedFile::fake()->create('report.pdf', 10, 'application/pdf');
        $saved = $this->fileService->storeFiles($this->record, [$file], $this->admin->id);

        $response = $this->actingAs($this->viewer)
            ->get("/record-files/{$saved[0]->id}/download");

        $response->assertStatus(200);
    }

    /** @test */
    public function editor_can_delete_file_removes_from_db_and_disk(): void
    {
        $file  = UploadedFile::fake()->create('delete_me.pdf', 5);
        $saved = $this->fileService->storeFiles($this->record, [$file], $this->admin->id);
        $rf    = $saved[0];

        // Verify file exists on disk
        Storage::disk('local')->assertExists("private/{$rf->storage_path}");

        $response = $this->actingAs($this->admin)
            ->delete("/record-files/{$rf->id}");

        $response->assertRedirect();

        // DB record gone
        $this->assertDatabaseMissing('record_files', ['id' => $rf->id]);

        // Physical file deleted
        Storage::disk('local')->assertMissing("private/{$rf->storage_path}");
    }

    /** @test */
    public function viewer_cannot_delete_file(): void
    {
        $file  = UploadedFile::fake()->create('test.pdf', 5);
        $saved = $this->fileService->storeFiles($this->record, [$file], $this->admin->id);

        $response = $this->actingAs($this->viewer)
            ->delete("/record-files/{$saved[0]->id}");

        $response->assertStatus(403);
    }
}
