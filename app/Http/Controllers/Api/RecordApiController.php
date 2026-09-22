<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Record;
use App\Models\RecordFile;
use App\Services\RecordFileService;
use App\Services\RecordSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * RecordApiController
 *
 * Handles Record CRUD and File upload/download/delete for Codex API.
 * Task 10 (read), Task 11 (create), Task 12 (upload).
 * Auth: Sanctum Bearer Token + role check.
 */
class RecordApiController extends Controller
{
    public function __construct(
        private RecordSearchService $searchService,
        private RecordFileService   $fileService,
    ) {
    }

    // ========== Records ==========

    /**
     * GET /api/records  — Viewer+ 可查詢
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeRole('viewer');

        $filters = $request->only([
            'project_id', 'module_id', 'type', 'keyword',
            'source', 'date_from', 'date_to',
        ]);

        $records = $this->searchService->search($filters)
            ->with(['project:id,name', 'module:id,name', 'files'])
            ->paginate(50);

        return response()->json($records);
    }

    /**
     * POST /api/records  — Editor+ 才能建立
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorizeRole('editor');

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'module_id'  => 'nullable|exists:modules,id',
            'type'       => 'required|in:development,test,issue,note',
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'source'     => 'required|in:manual,codex,agent,api',
            'ai_tool'    => 'nullable|string|max:50',
            'git_branch' => 'nullable|string|max:255',
            'git_commit' => 'nullable|string|max:100',
        ]);

        $validated['created_by'] = $request->user()->id;

        $record = Record::create($validated);
        $record->load(['project:id,name', 'module:id,name']);

        return response()->json($record, 201);
    }

    /**
     * GET /api/records/{id}  — Viewer+ 可查詢
     */
    public function show(Record $record): JsonResponse
    {
        $this->authorizeRole('viewer');

        $record->load(['project', 'module', 'files']);

        return response()->json($record);
    }

    /**
     * PUT /api/records/{id}  — Editor+ 才能修改
     */
    public function update(Request $request, Record $record): JsonResponse
    {
        $this->authorizeRole('editor');

        $validated = $request->validate([
            'project_id' => 'sometimes|required|exists:projects,id',
            'module_id'  => 'nullable|exists:modules,id',
            'type'       => 'sometimes|required|in:development,test,issue,note',
            'title'      => 'sometimes|required|string|max:255',
            'content'    => 'sometimes|required|string',
            'source'     => 'sometimes|required|in:manual,codex,agent,api',
            'ai_tool'    => 'nullable|string|max:50',
            'git_branch' => 'nullable|string|max:255',
            'git_commit' => 'nullable|string|max:100',
        ]);

        $record->update($validated);

        return response()->json($record->fresh(['project:id,name', 'module:id,name']));
    }

    // ========== Files ==========

    /**
     * POST /api/records/{record}/files  — Editor+ 才能上傳
     * 問題三修復：加入 max:20480 (20MB) 限制
     */
    public function fileStore(Request $request, Record $record): JsonResponse
    {
        $this->authorizeRole('editor');

        $request->validate([
            'files'   => 'required|array',
            'files.*' => 'file|max:20480',   // ← 20 MB 上限
        ]);

        $saved = $this->fileService->storeFiles(
            $record,
            $request->file('files'),
            $request->user()->id
        );

        return response()->json($saved, 201);
    }

    /**
     * PUT /api/record-files/{id}  — Editor+ 才能修改
     */
    public function fileUpdate(Request $request, RecordFile $recordFile): JsonResponse
    {
        $this->authorizeRole('editor');

        $validated = $request->validate([
            'display_name' => 'nullable|string|max:255',
            'note'         => 'nullable|string',
            'sort_order'   => 'nullable|integer',
        ]);

        $recordFile->update(array_filter($validated, fn($v) => $v !== null));

        return response()->json($recordFile->fresh());
    }

    /**
     * GET /api/record-files/{id}/download  — Viewer+ 可下載
     */
    public function fileDownload(RecordFile $recordFile): mixed
    {
        $this->authorizeRole('viewer');

        $absolutePath = $this->fileService->absolutePath($recordFile);

        if ($absolutePath === null) {
            return response()->json(['message' => '檔案不存在。'], 404);
        }

        return response()->download(
            $absolutePath,
            $recordFile->display_name . '.' . $recordFile->extension
        );
    }

    /**
     * DELETE /api/record-files/{id}  — Editor+ 才能刪除
     */
    public function fileDestroy(RecordFile $recordFile): JsonResponse
    {
        $this->authorizeRole('editor');

        $this->fileService->delete($recordFile);

        return response()->json(['message' => '附件已刪除。']);
    }
}
