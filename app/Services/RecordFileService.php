<?php

namespace App\Services;

use App\Models\Record;
use App\Models\RecordFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * RecordFileService
 *
 * Handles file upload (UUID-based storage path) and deletion per spec §15, §31.
 *
 * Security rule (spec §31):
 * - 所有實體檔案操作（刪除 / 讀取）前，必須確認 storage_path 在允許目錄（records/）內。
 * - 禁止操作 records/ 以外的任何路徑，防止 Path Traversal。
 */
class RecordFileService
{
    /**
     * 允許的根目錄前綴（含尾部斜線）。
     * 所有 storage_path 都必須以此開頭。
     */
    private const ALLOWED_PREFIX = 'records/';

    // ----------------------------------------------------------------
    // Upload
    // ----------------------------------------------------------------

    /**
     * Store uploaded files for a record.
     *
     * @param  Record  $record
     * @param  UploadedFile[]  $files
     * @param  int  $uploadedBy
     * @return RecordFile[]
     */
    public function storeFiles(Record $record, array $files, int $uploadedBy): array
    {
        $saved    = [];
        $sortBase = $record->files()->max('sort_order') ?? -1;

        foreach ($files as $index => $file) {
            $extension   = $file->getClientOriginalExtension();
            $uuid        = Str::uuid()->toString();
            $yearMonth   = now()->format('Y/m');
            $storagePath = self::ALLOWED_PREFIX . "{$yearMonth}/{$uuid}.{$extension}";

            // Sanity-check before writing (defense in depth)
            $this->assertWithinAllowedDirectory($storagePath);

            Storage::disk('local')->put("private/{$storagePath}", $file->getContent());

            $recordFile = RecordFile::create([
                'record_id'     => $record->id,
                'original_name' => $file->getClientOriginalName(),
                'display_name'  => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'note'          => null,
                'storage_path'  => $storagePath,
                'mime_type'     => $file->getMimeType(),
                'extension'     => $extension,
                'file_size'     => $file->getSize(),
                'sort_order'    => $sortBase + $index + 1,
                'uploaded_by'   => $uploadedBy,
            ]);

            $saved[] = $recordFile;
        }

        return $saved;
    }

    // ----------------------------------------------------------------
    // Delete
    // ----------------------------------------------------------------

    /**
     * Delete a RecordFile: remove DB record and physical file.
     *
     * 刪除前必須確認：
     *  1. storage_path 在 records/ 目錄內（防止 Path Traversal）
     *  2. Caller 已驗證權限（Controller 層負責）
     *
     * @throws \RuntimeException  若 storage_path 不在允許目錄內
     */
    public function delete(RecordFile $recordFile): void
    {
        // ★ 安全檢查：禁止刪除 records/ 目錄以外的資料
        $this->assertWithinAllowedDirectory($recordFile->storage_path);

        $path = "private/{$recordFile->storage_path}";

        Storage::disk('local')->delete($path);

        $recordFile->delete();
    }

    // ----------------------------------------------------------------
    // Read / Download
    // ----------------------------------------------------------------

    /**
     * Get the absolute file path for download/preview.
     *
     * 讀取前必須確認 storage_path 在 records/ 目錄內。
     * Returns null if file does not exist on disk.
     *
     * @throws \RuntimeException  若 storage_path 不在允許目錄內
     */
    public function absolutePath(RecordFile $recordFile): ?string
    {
        // ★ 安全檢查：禁止存取 records/ 目錄以外的資料
        $this->assertWithinAllowedDirectory($recordFile->storage_path);

        $path = "private/{$recordFile->storage_path}";

        if (!Storage::disk('local')->exists($path)) {
            return null;
        }

        return Storage::disk('local')->path($path);
    }

    // ----------------------------------------------------------------
    // Internal guard
    // ----------------------------------------------------------------

    /**
     * 確認 storage_path 在允許目錄（records/）內。
     *
     * 規則：
     * - 必須以 "records/" 開頭
     * - Resolved 路徑不得跳出根目錄（防止 ../ traversal）
     *
     * @throws \RuntimeException  若路徑不合法
     */
    private function assertWithinAllowedDirectory(string $storagePath): void
    {
        // 1. 前綴檢查
        if (!str_starts_with($storagePath, self::ALLOWED_PREFIX)) {
            throw new \RuntimeException(
                "非法路徑：storage_path 必須在 " . self::ALLOWED_PREFIX . " 內，收到：{$storagePath}"
            );
        }

        // 2. Path traversal 檢查（解析 ../ 後必須仍在 allowed prefix 下）
        $normalised = $this->normalisePath($storagePath);

        if (!str_starts_with($normalised, self::ALLOWED_PREFIX)) {
            throw new \RuntimeException(
                "非法路徑（Path Traversal）：解析後路徑 {$normalised} 不在允許目錄內。"
            );
        }
    }

    /**
     * Normalise a relative path by resolving ../ segments.
     * Does not touch the filesystem — pure string operation.
     */
    private function normalisePath(string $path): string
    {
        $parts  = [];
        $segments = explode('/', str_replace('\\', '/', $path));

        foreach ($segments as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }
            if ($segment === '..') {
                array_pop($parts);
            } else {
                $parts[] = $segment;
            }
        }

        return implode('/', $parts) . (str_ends_with($path, '/') ? '/' : '');
    }
}
