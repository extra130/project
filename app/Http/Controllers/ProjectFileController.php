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
            'files.*' => ['file', 'max:20480', function ($attribute, $value, $fail) {
                $ext = strtolower($value->getClientOriginalExtension());
                if (in_array($ext, ['html', 'htm', 'svg', 'php', 'php5', 'phtml', 'exe', 'sh', 'bat', 'js'])) {
                    $fail('基於安全性考量，禁止上傳該類型的檔案。');
                }
            }],
        ]);

        $this->fileService->storeFiles($project, $request->file('files'), Auth::id());

        return back()->with('success', '專案檔案已上傳。');
    }

    /**
     * Download a project file.
     */
    public function download(ProjectFile $projectFile): StreamedResponse
    {
        $this->authorizeRole('viewer');

        $path = $projectFile->storage_path;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, '檔案不存在。');
        }

        return Storage::disk('local')->download($path, $projectFile->original_name);
    }

    /**
     * Preview a project file online.
     */
    public function preview(ProjectFile $projectFile)
    {
        $this->authorizeRole('viewer');

        $path = $projectFile->storage_path;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, '檔案不存在。');
        }

        $absolutePath = Storage::disk('local')->path($path);
        $ext = strtolower($projectFile->extension);

        // Drawio: Render via static diagram viewer
        if ($ext === 'drawio') {
            $content = file_get_contents($absolutePath);
            $fileName = $projectFile->original_name;
            return view('shared.preview-drawio', compact('fileName', 'content'));
        }

        // Markdown: Render as HTML view
        if (in_array($ext, ['md', 'markdown'])) {
            $content = file_get_contents($absolutePath);
            // We can reuse the same markdown preview view but pass a different variable or just use generic name.
            // Let's pass it as $recordFile since the view expects it, or create a new view for project_files.
            // Wait, the view uses $recordFile->display_name. ProjectFile doesn't have display_name, it uses original_name.
            // So we need a separate view or a generic one. Let's create `resources/views/project_files/preview-md.blade.php`.
            return view('project_files.preview-md', compact('projectFile', 'content'));
        }

        $mime = $projectFile->mime_type ?? mime_content_type($absolutePath);

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $projectFile->original_name . '"'
        ]);
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

