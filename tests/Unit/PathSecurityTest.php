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
 * 路徑安全測試（spec §31）
 *
 * 規則：
 * - 禁止刪除 records/ 目錄以外的資料
 * - 禁止存取 records/ 目錄以外的資料（下載 / absolutePath）
 * - Path Traversal（../）必須被攔截
 */
class PathSecurityTest extends TestCase
{
    use RefreshDatabase;

    private RecordFileService $fileService;
    private Record            $record;
    private User              $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->admin       = User::factory()->create(['role' => 'admin']);
        $project           = Project::factory()->create();
        $this->record      = Record::factory()->create(['project_id' => $project->id]);
        $this->fileService = app(RecordFileService::class);
    }

    // ────────────────────────────────────────────
    // 上傳：storage_path 必須以 records/ 開頭
    // ────────────────────────────────────────────

    /** @test */
    public function upload_generates_path_inside_records_directory(): void
    {
        $file  = UploadedFile::fake()->create('test.pdf', 10);
        $saved = $this->fileService->storeFiles($this->record, [$file], $this->admin->id);

        $this->assertStringStartsWith('records/', $saved[0]->storage_path);
    }

    /** @test */
    public function upload_path_does_not_contain_original_filename(): void
    {
        $file  = UploadedFile::fake()->create('../../etc/passwd.txt', 5);
        $saved = $this->fileService->storeFiles($this->record, [$file], $this->admin->id);

        // storage_path 不含原始檔名，只有 UUID
        $this->assertStringNotContainsString('passwd', $saved[0]->storage_path);
        $this->assertStringStartsWith('records/', $saved[0]->storage_path);
    }

    // ────────────────────────────────────────────
    // 刪除：禁止 records/ 以外的路徑
    // ────────────────────────────────────────────

    /** @test */
    public function delete_throws_if_storage_path_is_outside_records_directory(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/非法路徑/');

        // 手動建立一個 storage_path 指向 records/ 以外的 RecordFile
        $badFile = RecordFile::factory()->create([
            'record_id'    => $this->record->id,
            'storage_path' => 'config/app.php',   // ← 不在 records/ 內
        ]);

        $this->fileService->delete($badFile);
    }

    /** @test */
    public function delete_throws_on_path_traversal_attempt(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/非法路徑/');

        $badFile = RecordFile::factory()->create([
            'record_id'    => $this->record->id,
            'storage_path' => 'records/../../.env',  // ← traversal
        ]);

        $this->fileService->delete($badFile);
    }

    /** @test */
    public function delete_succeeds_for_valid_records_path(): void
    {
        $file  = UploadedFile::fake()->create('valid.pdf', 10);
        $saved = $this->fileService->storeFiles($this->record, [$file], $this->admin->id);

        // Should not throw
        $this->fileService->delete($saved[0]);

        $this->assertDatabaseMissing('record_files', ['id' => $saved[0]->id]);
    }

    // ────────────────────────────────────────────
    // 下載 / absolutePath：禁止 records/ 以外的路徑
    // ────────────────────────────────────────────

    /** @test */
    public function absolute_path_throws_if_storage_path_is_outside_records_directory(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/非法路徑/');

        $badFile = RecordFile::factory()->create([
            'record_id'    => $this->record->id,
            'storage_path' => '../private/.env',   // ← 不在 records/ 內
        ]);

        $this->fileService->absolutePath($badFile);
    }

    /** @test */
    public function absolute_path_throws_on_path_traversal(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/非法路徑/');

        $badFile = RecordFile::factory()->create([
            'record_id'    => $this->record->id,
            'storage_path' => 'records/../../../.env',
        ]);

        $this->fileService->absolutePath($badFile);
    }

    /** @test */
    public function absolute_path_returns_null_when_file_missing_but_path_valid(): void
    {
        // 合法路徑但磁碟上不存在
        $file = RecordFile::factory()->create([
            'record_id'    => $this->record->id,
            'storage_path' => 'records/2026/09/nonexistent-uuid.pdf',
        ]);

        $result = $this->fileService->absolutePath($file);

        $this->assertNull($result);
    }

    // ────────────────────────────────────────────
    // 下載 HTTP 端點：對 records/ 外路徑的 RecordFile
    // Controller 應正常回應（RuntimeException → 500 或由 handler 處理）
    // ────────────────────────────────────────────

    /** @test */
    public function download_endpoint_throws_for_file_outside_records_directory(): void
    {
        $badFile = RecordFile::factory()->create([
            'record_id'    => $this->record->id,
            'storage_path' => 'config/app.php',
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/record-files/{$badFile->id}/download");

        // RuntimeException は 500 として処理される
        $response->assertStatus(500);
    }
}
