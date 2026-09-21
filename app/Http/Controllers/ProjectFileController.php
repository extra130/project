<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use App\Services\ProjectFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectFileController extends Controller
{
    public function __construct(private ProjectFileService $fileService)
    {
    }

    /**
     * Upload files to a project.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorizeRole('editor');

        $request->validate([
            'files'   => 'required|array',
            'files.*' => 'file|max:20480', // 20MB max per file
        ]);

        $this->fileService->storeFiles($project, $request->file('files'), Auth::id());

        return back()->with('success', '專案檔案已上傳。');
    }

    /**
     * Download a project file.
     */
    public function download(ProjectFile $projectFile): StreamedResponse
    {
        $path = $projectFile->storage_path;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, '檔案不存在。');
        }

        return Storage::disk('local')->download($path, $projectFile->original_name);
    }

    /**
     * Delete a project file.
     */
    public function destroy(ProjectFile $projectFile)
    {
        $this->authorizeRole('editor');

        $this->fileService->deleteFile($projectFile);

        return back()->with('success', '專案檔案已刪除。');
    }
}
