<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Project;
use App\Models\Record;
use App\Services\RecordSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RecordController extends Controller
{
    public function __construct(private RecordSearchService $searchService)
    {
    }

    /**
     * Display records with optional search filters (Task 04 + 05).
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'project_id', 'module_id', 'type', 'keyword',
            'source', 'date_from', 'date_to', 'tag',
        ]);

        $records = $this->searchService->search($filters)->paginate(20)->withQueryString();

        $projects = Project::orderBy('sort_order')->orderBy('name')->get();
        $modules  = !empty($filters['project_id'])
            ? Module::where('project_id', $filters['project_id'])->orderBy('sort_order')->get()
            : collect();

        return view('records.index', compact('records', 'projects', 'modules', 'filters'));
    }

    /**
     * Show form to create a record.
     */
    public function create(Request $request)
    {
        $this->authorizeRole('editor');

        $projects = Project::active()->orderBy('sort_order')->orderBy('name')->get();
        $projectId = filter_var($request->old('project_id', $request->query('project_id')), FILTER_VALIDATE_INT);
        $selectedProject = $projectId !== false
            ? Project::find($projectId)
            : null;

        $modules = $selectedProject
            ? $selectedProject->modules()->active()->get()
            : collect();

        $selectedModule = $modules->firstWhere('id', $request->query('module_id'));
        $moduleOptions = Module::whereIn('project_id', $projects->modelKeys())->active()
            ->orderBy('sort_order')->get(['id', 'project_id', 'name'])->groupBy('project_id');

        return view('records.create', compact('projects', 'modules', 'selectedProject', 'selectedModule', 'moduleOptions'));
    }

    /**
     * Store a new record.
     */
    public function store(Request $request)
    {
        $this->authorizeRole('editor');

        $projectId = filter_var($request->input('project_id'), FILTER_VALIDATE_INT);
        $validated = $request->validate([
            'project_id' => 'bail|required|integer|exists:projects,id',
            'module_id'  => ['bail', 'nullable', 'integer', Rule::exists('modules', 'id')->where('project_id', $projectId === false ? null : $projectId)],
            'type'       => 'required|in:development,test,issue,note',
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'source'     => 'required|in:manual,codex,agent,api',
            'ai_tool'    => 'nullable|string|max:50',
            'git_branch' => 'nullable|string|max:255',
            'git_commit' => 'nullable|string|max:100',
            'tags_input' => 'nullable|string|max:255',
            'files'      => 'nullable|array',
            'files.*'    => 'file|max:20480', // 最大 20MB
        ]);

        $recordData = collect($validated)->except(['files', 'tags_input'])->toArray();
        $recordData['created_by'] = Auth::id();

        $savedFiles = [];
        try {
            $record = DB::transaction(function () use ($recordData, $request, &$savedFiles) {
                $record = Record::create($recordData);
                $this->syncTags($record, $request->input('tags_input'));

                // 如果有夾帶檔案，直接呼叫 RecordFileService 儲存
                if ($request->hasFile('files')) {
                    $savedFiles = app(\App\Services\RecordFileService::class)->storeFiles(
                        $record,
                        $request->file('files'),
                        Auth::id()
                    );
                }

                return $record;
            });
        } catch (\Throwable $e) {
            // 附件服務成功返回後，最外層交易仍可能提交失敗。
            // 僅清除本次服務回傳的 UUID 檔案，不再依賴失敗的 DB 連線。
            foreach ($savedFiles as $file) {
                Storage::disk('local')->delete("private/{$file->storage_path}");
            }
            throw $e;
        }

        return redirect()->route('records.show', $record)
            ->with('success', '紀錄已建立。');
    }

    /**
     * Display a single record with its files.
     */
    public function show(Record $record)
    {
        $record->load(['project', 'module', 'files', 'tags']);

        return view('records.show', compact('record'));
    }

    /**
     * Show edit form.
     */
    public function edit(Request $request, Record $record)
    {
        $this->authorizeRole('editor');

        $projects = Project::orderBy('sort_order')->orderBy('name')->get();
        $projectId = filter_var($request->old('project_id', $record->project_id), FILTER_VALIDATE_INT);
        $modules  = Module::where('project_id', $projectId === false ? null : $projectId)
            ->orderBy('sort_order')->get();
        $moduleOptions = Module::whereIn('project_id', $projects->modelKeys())
            ->orderBy('sort_order')->get(['id', 'project_id', 'name'])->groupBy('project_id');
            
        $tagsString = $record->tags->pluck('name')->implode(', ');

        return view('records.edit', compact('record', 'projects', 'modules', 'tagsString', 'moduleOptions'));
    }

    /**
     * Update a record.
     */
    public function update(Request $request, Record $record)
    {
        $this->authorizeRole('editor');

        $projectId = filter_var($request->input('project_id'), FILTER_VALIDATE_INT);
        $validated = $request->validate([
            'project_id' => 'bail|required|integer|exists:projects,id',
            'module_id'  => ['bail', 'nullable', 'integer', Rule::exists('modules', 'id')->where('project_id', $projectId === false ? null : $projectId)],
            'type'       => 'required|in:development,test,issue,note',
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'source'     => 'required|in:manual,codex,agent,api',
            'ai_tool'    => 'nullable|string|max:50',
            'git_branch' => 'nullable|string|max:255',
            'git_commit' => 'nullable|string|max:100',
            'tags_input' => 'nullable|string|max:255',
        ]);

        if (!array_key_exists('module_id', $validated) && $record->module_id !== null
            && !Module::whereKey($record->module_id)->where('project_id', $validated['project_id'])->exists()) {
            throw ValidationException::withMessages(['module_id' => '模組必須屬於所選專案。']);
        }

        DB::transaction(function () use ($record, $validated, $request) {
            $recordData = collect($validated)->except('tags_input')->toArray();
            $record->update($recordData);

            $this->syncTags($record, $request->input('tags_input'));
        });

        return redirect()->route('records.show', $record)
            ->with('success', '紀錄已更新。');
    }

    // ---------- helper ----------

    /**
     * 處理並同步標籤
     */
    private function syncTags(Record $record, ?string $tagsInput): void
    {
        if (!$tagsInput) {
            $record->tags()->sync([]);
            return;
        }

        // 以逗號分隔，去除空白
        $tagNames = collect(explode(',', $tagsInput))
            ->map(fn($name) => trim($name))
            ->filter(fn($name) => $name !== '');

        $tagIds = [];
        foreach ($tagNames as $name) {
            if (mb_strlen($name) > 50) {
                throw ValidationException::withMessages(['tags_input' => '每個標籤最多可輸入 50 個字元。']);
            }
            $tag = \App\Models\Tag::firstOrCreate(['name' => $name]);
            $tagIds[] = $tag->id;
        }

        $record->tags()->sync($tagIds);
    }
}
