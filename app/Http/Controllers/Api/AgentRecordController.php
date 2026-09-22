<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AgentRecordController
 *
 * Codex / AI Agent API per spec §21–28.
 * Authentication: Laravel Sanctum Bearer Token + role check.
 *
 * 權限規則：
 *   - Viewer+ 可查詢（GET）
 *   - Editor+ 才能建立/修改（POST / PUT）
 */
class AgentRecordController extends Controller
{
    // ========== Projects ==========

    /** GET /api/projects — Viewer+ */
    public function projectIndex(): JsonResponse
    {
        $this->authorizeRole('viewer');

        $projects = Project::active()->orderBy('name')->get([
            'id', 'name', 'description', 'color', 'status',
        ]);

        return response()->json($projects);
    }

    /** POST /api/projects — Editor+ */
    public function projectStore(Request $request): JsonResponse
    {
        $this->authorizeRole('editor');

        $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:10',
            'status'      => 'nullable|in:active,archived',
        ]);

        $project = Project::create([
            'name'        => $request->name,
            'description' => $request->description,
            'color'       => $request->color ?? '#4f46e5',
            'status'      => $request->status ?? 'active',
            'created_by'  => $request->user()->id,
        ]);

        return response()->json($project, 201);
    }

    /** GET /api/projects/{id} — Viewer+ */
    public function projectShow(Project $project): JsonResponse
    {
        $this->authorizeRole('viewer');

        return response()->json($project);
    }

    /** PUT /api/projects/{id} — Editor+ */
    public function projectUpdate(Request $request, Project $project): JsonResponse
    {
        $this->authorizeRole('editor');

        $request->validate([
            'name'        => 'sometimes|required|string|max:150',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:10',
            'status'      => 'nullable|in:active,archived',
        ]);

        $project->update($request->only(['name', 'description', 'color', 'status']));

        return response()->json($project);
    }

    // ========== Modules ==========

    /** GET /api/projects/{project}/modules — Viewer+ */
    public function moduleIndex(Project $project): JsonResponse
    {
        $this->authorizeRole('viewer');

        $modules = $project->modules()->get([
            'id', 'project_id', 'name', 'description', 'sort_order', 'status',
        ]);

        return response()->json($modules);
    }

    /** POST /api/projects/{project}/modules — Editor+ */
    public function moduleStore(Request $request, Project $project): JsonResponse
    {
        $this->authorizeRole('editor');

        $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
            'status'      => 'nullable|in:active,archived',
        ]);

        $module = $project->modules()->create([
            'name'        => $request->name,
            'description' => $request->description,
            'sort_order'  => $request->sort_order ?? 0,
            'status'      => $request->status ?? 'active',
        ]);

        return response()->json($module, 201);
    }

    /** GET /api/modules/{id} — Viewer+ */
    public function moduleShow(\App\Models\Module $module): JsonResponse
    {
        $this->authorizeRole('viewer');

        return response()->json($module->load('project'));
    }

    /** PUT /api/modules/{id} — Editor+ */
    public function moduleUpdate(Request $request, \App\Models\Module $module): JsonResponse
    {
        $this->authorizeRole('editor');

        $request->validate([
            'name'        => 'sometimes|required|string|max:150',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
            'status'      => 'nullable|in:active,archived',
        ]);

        $module->update($request->only(['name', 'description', 'sort_order', 'status']));

        return response()->json($module);
    }

    // ========== Project Files ==========

    /** POST /api/projects/{project}/files — Editor+ */
    public function fileStore(Request $request, Project $project, \App\Services\ProjectFileService $fileService): JsonResponse
    {
        $this->authorizeRole('editor');

        $request->validate([
            'files'   => 'required|array',
            'files.*' => 'file|max:20480',
        ]);

        $fileService->storeFiles($project, $request->file('files'), $request->user()->id);

        return response()->json($project->files()->get(), 201);
    }

    /** GET /api/project-files/{projectFile}/download — Viewer+ */
    public function fileDownload(\App\Models\ProjectFile $projectFile): mixed
    {
        $this->authorizeRole('viewer');

        $path = $projectFile->storage_path;

        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            return response()->json(['message' => '檔案不存在。'], 404);
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->download($path, $projectFile->original_name);
    }

    /** DELETE /api/project-files/{projectFile} — Editor+ */
    public function fileDestroy(\App\Models\ProjectFile $projectFile, \App\Services\ProjectFileService $fileService): JsonResponse
    {
        $this->authorizeRole('editor');

        $fileService->deleteFile($projectFile);

        return response()->json(['message' => '檔案已刪除。']);
    }
}
