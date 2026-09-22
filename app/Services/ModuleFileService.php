<?php

namespace App\Services;

use App\Models\Module;
use App\Models\ModuleFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ModuleFileService
{
    /**
     * Store multiple files for a module
     */
    public function storeFiles(Module $module, array $files, ?int $uploadedBy = null): void
    {
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }

            $this->storeSingleFile($module, $file, $uploadedBy);
        }
    }

    private function storeSingleFile(Module $module, UploadedFile $file, ?int $uploadedBy): ModuleFile
    {
        $originalName = $file->getClientOriginalName();
        $extension    = $file->getClientOriginalExtension() ?: $file->extension();
        $mimeType     = $file->getMimeType();
        $fileSize     = $file->getSize();

        // Path format: modules/YYYY/MM/{uuid}.ext
        $datePath = date('Y/m');
        $uuid = Str::uuid()->toString();
        $fileName = $extension ? "{$uuid}.{$extension}" : $uuid;
        
        $storagePath = "modules/{$datePath}/{$fileName}";

        // Store file
        Storage::disk('local')->putFileAs("modules/{$datePath}", $file, $fileName);

        // Save DB record
        return ModuleFile::create([
            'module_id'     => $module->id,
            'original_name' => $originalName,
            'storage_path'  => $storagePath,
            'mime_type'     => $mimeType,
            'extension'     => strtolower($extension),
            'file_size'     => $fileSize,
            'uploaded_by'   => $uploadedBy,
        ]);
    }

    /**
     * Remove a single file from DB and disk
     */
    public function deleteFile(ModuleFile $moduleFile): bool
    {
        $path = $moduleFile->storage_path;
        
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        return $moduleFile->delete();
    }
}
