<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectFileService
{
    /**
     * Store multiple files for a project
     */
    public function storeFiles(Project $project, array $files, ?int $uploadedBy = null): void
    {
        $maxSort = $project->files()->max('sort_order') ?? 0;

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }

            $maxSort++;
            $this->storeSingleFile($project, $file, $maxSort, $uploadedBy);
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

        // Store file
        Storage::disk('local')->putFileAs("projects/{$datePath}", $file, $fileName);

        // Save DB record
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
