<?php

namespace App\Services;

use App\Models\Module;
use App\Models\ModuleFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ModuleFileService
{
    /**
     * Store multiple files for a module
     */
    public function storeFiles(Module $module, array $files, ?int $uploadedBy = null): void
    {
        $saved = [];
        try {
            DB::transaction(function () use ($module, $files, $uploadedBy, &$saved) {
                foreach ($files as $file) {
                    if (!$file instanceof UploadedFile || !$file->isValid()) {
                        continue;
                    }

                    $saved[] = $this->storeSingleFile($module, $file, $uploadedBy);
                }
            });
        } catch (\Throwable $e) {
            foreach ($saved as $file) {
                Storage::disk('local')->delete($file->storage_path);
            }
            throw $e;
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

        try {
            if (Storage::disk('local')->putFileAs("modules/{$datePath}", $file, $fileName) === false) {
                throw ValidationException::withMessages(['files' => '檔案儲存失敗，請稍後再試。']);
            }

            return ModuleFile::create([
                'module_id'     => $module->id,
                'original_name' => $originalName,
                'storage_path'  => $storagePath,
                'mime_type'     => $mimeType,
                'extension'     => strtolower($extension),
                'file_size'     => $fileSize,
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
    public function deleteFile(ModuleFile $moduleFile): bool
    {
        $path = $moduleFile->storage_path;
        
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        return $moduleFile->delete();
    }
}
