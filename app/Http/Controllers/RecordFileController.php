<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\RecordFile;
use App\Services\RecordFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * RecordFileController
 *
 * Handles Task 06 (upload), Task 07 (rename/note/sort), Task 08 (preview/download/delete).
 */
class RecordFileController extends Controller
{
    public function __construct(private RecordFileService $fileService)
    {
    }

    /**
     * Task 06: Upload multiple files to a record.
     * POST /records/{record}/files
     */
    public function store(Request $request, Record $record)
    {
        $this->authorizeRole('editor');

        $request->validate([
            'files'   => 'required|array',
            'files.*' => ['file', function ($attribute, $value, $fail) {
                $ext = strtolower($value->getClientOriginalExtension());
                if (in_array($ext, ['html', 'htm', 'svg', 'php', 'php5', 'phtml', 'exe', 'sh', 'bat', 'js'])) {
                    $fail('基於安全性考量，禁止上傳該類型的檔案。');
                }
            }],
        ]);

        $this->fileService->storeFiles(
            $record,
            $request->file('files'),
            Auth::id()
        );

        return redirect()->route('records.show', $record)
            ->with('success', '附件已上傳。');
    }

    /**
     * Task 07: Show edit form for a file's display_name / note / sort_order.
     * GET /record-files/{id}/edit
     */
    public function edit(RecordFile $recordFile)
    {
        $this->authorizeRole('editor');

        return view('record_files.edit', compact('recordFile'));
    }

    /**
     * Task 07: Update display_name / note / sort_order only.
     * PUT /record-files/{id}
     * original_name is never modified here.
     */
    public function update(Request $request, RecordFile $recordFile)
    {
        $this->authorizeRole('editor');

        $validated = $request->validate([
            'display_name' => 'nullable|string|max:255',
            'note'         => 'nullable|string',
            'sort_order'   => 'nullable|integer',
        ]);

        $recordFile->update(array_filter($validated, fn($v) => $v !== null));

        return redirect()->route('records.show', $recordFile->record_id)
            ->with('success', '附件資訊已更新。');
    }

    /**
     * Task 08: Download a file through controller (enforces auth, no direct public path).
     * GET /record-files/{id}/download
     */
    public function download(RecordFile $recordFile)
    {
        // Viewer+ can download
        if (!Auth::user()->isViewer()) {
            abort(403);
        }

        $absolutePath = $this->fileService->absolutePath($recordFile);

        if ($absolutePath === null) {
            abort(404, '檔案不存在。');
        }

        return response()->download(
            $absolutePath,
            $recordFile->display_name . '.' . $recordFile->extension
        );
    }

    /**
     * Preview file online (PDF, HTML, MD, etc.)
     * GET /record-files/{id}/preview
     */
    public function preview(RecordFile $recordFile)
    {
        if (!Auth::user()->isViewer()) {
            abort(403);
        }

        $absolutePath = $this->fileService->absolutePath($recordFile);

        if ($absolutePath === null) {
            abort(404, '檔案不存在。');
        }

        $ext = strtolower($recordFile->extension);

        // Drawio: Render via static diagram viewer
        if ($ext === 'drawio') {
            $content = file_get_contents($absolutePath);
            $fileName = $recordFile->display_name . '.' . $ext;
            return view('shared.preview-drawio', compact('fileName', 'content'));
        }

        // Markdown: Render as HTML view
        if (in_array($ext, ['md', 'markdown'])) {
            $content = file_get_contents($absolutePath);
            return view('record_files.preview-md', compact('recordFile', 'content'));
        }

        // Other supported preview formats: inline file response
        $mime = $recordFile->mime_type ?? mime_content_type($absolutePath);
        
        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $recordFile->display_name . '.' . $ext . '"'
        ]);
    }

    /**
     * Task 08: Delete a file (DB + physical).
     * DELETE /record-files/{id}
     */
    public function destroy(RecordFile $recordFile)
    {
        $this->authorizeRole('editor');

        $recordId = $recordFile->record_id;

        $this->fileService->delete($recordFile);

        return redirect()->route('records.show', $recordId)
            ->with('success', '附件已刪除。');
    }

}

