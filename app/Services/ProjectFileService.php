<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProjectFileService
{
    /**
     * Store multiple files for a project
     */
    public function storeFiles(Project $project, array $files, ?int $uploadedBy = null): void
    {
        $maxSort = $project->files()->max('sort_order') ?? 0;

        $saved = [];
        try {
            DB::transaction(function () use ($project, $files, $uploadedBy, $maxSort, &$saved) {
                foreach ($files as $file) {
                    if (!$file instanceof UploadedFile || !$file->isValid()) {
                        continue;
                    }

                    $maxSort++;
                    $saved[] = $this->storeSingleFile($project, $file, $maxSort, $uploadedBy);
                }
            });
        } catch (\Throwable $e) {
            foreach ($saved as $file) {
                Storage::disk('local')->delete($file->storage_path);
            }
            throw $e;
        }
    }

    private function storeSingleFile(Project $project, UploadedFile $file, int $sortOrder, ?int $uploadedBy): ProjectFile
    {
        $originalName = $file->getClientOriginalName();
        $extension    = $file->getClientOriginalExtension() ?: $file->extension();
        $mimeType     = $file->getMimeType();
        $fileSize     = $file->getSize();

        // Path format: projects/YYYY/MM/{uuid}.ext
        $datePath = date('Y/m');
        $uuid = Str::uuid()->toString();
        $fileName = $extension ? "{$uuid}.{$extension}" : $uuid;
        
        $storagePath = "projects/{$datePath}/{$fileName}";

        try {
            if (Storage::disk('local')->putFileAs("projects/{$datePath}", $file, $fileName) === false) {
                throw ValidationException::withMessages(['files' => '檔案儲存失敗，請稍後再試。']);
            }

            return ProjectFile::create([
                'project_id'    => $project->id,
                'original_name' => $originalName,
                'display_name'  => $originalName,
                'storage_path'  => $storagePath,
                'mime_type'     => $mimeType,
                'extension'     => strtolower($extension),
                'file_size'     => $fileSize,
                'sort_order'    => $sortOrder,
                'uploaded_by'   => $uploadedBy,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($storagePath);
            throw $e;
        }
    }

    /**
     * Remove a single file from DB and disk
     */
    public function deleteFile(ProjectFile $projectFile): bool
    {
        $path = $projectFile->storage_path;
        
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        return $projectFile->delete();
    }
}
