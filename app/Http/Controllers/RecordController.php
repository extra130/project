<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Project;
use App\Models\Record;
use App\Services\RecordSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'source', 'date_from', 'date_to',
        ]);

        $records = $this->searchService->search($filters)->paginate(20)->withQueryString();

        $projects = Project::orderBy('name')->get();
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

        $projects = Project::active()->orderBy('name')->get();
        $selectedProject = $request->query('project_id')
            ? Project::find($request->query('project_id'))
            : null;

        $modules = $selectedProject
            ? $selectedProject->modules()->active()->get()
            : collect();

        return view('records.create', compact('projects', 'modules', 'selectedProject'));
    }

    /**
     * Store a new record.
     */
    public function store(Request $request)
    {
        $this->authorizeRole('editor');

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'module_id'  => 'nullable|exists:modules,id',
            'type'       => 'required|in:development,test,issue,note',
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'source'     => 'required|in:manual,codex,agent,api',
            'git_branch' => 'nullable|string|max:255',
            'git_commit' => 'nullable|string|max:100',
            'files'      => 'nullable|array',
            'files.*'    => 'file|max:20480', // 最大 20MB
        ]);

        $recordData = collect($validated)->except('files')->toArray();
        $recordData['created_by'] = Auth::id();

        $record = Record::create($recordData);
        $this->syncTags($record, $request->input('tags_input'));

        // 如果有夾帶檔案，直接呼叫 RecordFileService 儲存
        if ($request->hasFile('files')) {
            app(\App\Services\RecordFileService::class)->storeFiles(
                $record,
                $request->file('files'),
                Auth::id()
            );
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
    public function edit(Record $record)
    {
        $this->authorizeRole('editor');

        $projects = Project::orderBy('name')->get();
        $modules  = Module::where('project_id', $record->project_id)
            ->orderBy('sort_order')->get();
            
        $tagsString = $record->tags->pluck('name')->implode(', ');

        return view('records.edit', compact('record', 'projects', 'modules', 'tagsString'));
    }

    /**
     * Update a record.
     */
    public function update(Request $request, Record $record)
    {
        $this->authorizeRole('editor');

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'module_id'  => 'nullable|exists:modules,id',
            'type'       => 'required|in:development,test,issue,note',
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'source'     => 'required|in:manual,codex,agent,api',
            'git_branch' => 'nullable|string|max:255',
            'git_commit' => 'nullable|string|max:100',
            'tags_input' => 'nullable|string|max:255',
        ]);

        $recordData = collect($validated)->except('tags_input')->toArray();
        $record->update($recordData);

        $this->syncTags($record, $request->input('tags_input'));

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
            $tag = \App\Models\Tag::firstOrCreate(['name' => $name]);
            $tagIds[] = $tag->id;
        }

        $record->tags()->sync($tagIds);
    }
}
