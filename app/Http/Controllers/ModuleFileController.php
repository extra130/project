<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\ModuleFile;
use App\Services\ModuleFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ModuleFileController extends Controller
{
    public function __construct(private ModuleFileService $fileService)
    {
    }

    /**
     * Upload files to a module.
     */
    public function store(Request $request, Module $module)
    {
        $this->authorizeRole('editor');

        $request->validate([
            'files'   => 'required|array',
            'files.*' => 'file|max:20480', // 20MB max per file
        ]);

        $this->fileService->storeFiles($module, $request->file('files'), Auth::id());

        return back()->with('success', '模組文件上傳成功！');
    }

    /**
     * Download a module file.
     */
    public function download(ModuleFile $moduleFile): StreamedResponse
    {
        $this->authorizeRole('viewer');

        $path = $moduleFile->storage_path;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, '檔案不存在。');
        }

        return Storage::disk('local')->download($path, $moduleFile->original_name);
    }

    /**
     * Preview a module file online.
     */
    public function preview(ModuleFile $moduleFile)
    {
        $this->authorizeRole('viewer');

        $path = $moduleFile->storage_path;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, '檔案不存在。');
        }

        $absolutePath = Storage::disk('local')->path($path);
        $ext = strtolower($moduleFile->extension);

        // Drawio: Render via static diagram viewer
        if ($ext === 'drawio') {
            $content = file_get_contents($absolutePath);
            $fileName = $moduleFile->original_name;
            return view('shared.preview-drawio', compact('fileName', 'content'));
        }

        // Markdown: Render as HTML view
        if (in_array($ext, ['md', 'markdown'])) {
            $content = file_get_contents($absolutePath);
            return view('module_files.preview-md', compact('moduleFile', 'content'));
        }

        $mime = $moduleFile->mime_type ?? mime_content_type($absolutePath);

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $moduleFile->original_name . '"'
        ]);
    }

    /**
     * Delete a module file.
     */
    public function destroy(ModuleFile $moduleFile)
    {
        $this->authorizeRole('editor');

        $this->fileService->deleteFile($moduleFile);

        return back()->with('success', '模組文件已刪除！');
    }
}
