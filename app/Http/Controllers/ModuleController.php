<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    /**
     * Show modules list under a project.
     */
    public function index(Project $project)
    {
        $modules = $project->modules()->get();

        return view('modules.index', [
            'project'        => $project,
            'modules'        => $modules,
            'currentProject' => $project,
        ]);
    }

    /**
     * Show form to create module under a project.
     */
    public function create(Project $project)
    {
        $this->authorizeRole('editor');

        return view('modules.create', [
            'project'        => $project,
            'currentProject' => $project,
        ]);
    }

    /**
     * Store a new module.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorizeRole('editor');

        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
            'status'      => 'required|in:active,archived',
        ]);

        $validated['project_id'] = $project->id;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $module = Module::create($validated);

        return redirect()->route('projects.modules.index', $project)
            ->with('success', '模組已建立。');
    }

    /**
     * Show the form for editing a module.
     */
    public function edit(Module $module)
    {
        $this->authorizeRole('editor');

        return view('modules.edit', [
            'module'         => $module,
            'currentProject' => $module->project,
        ]);
    }

    /**
     * Display a module and all its record attachments.
     */
    public function show(Module $module)
    {
        $module->load('project');

        // 取得該模組底下所有紀錄的附件
        $files = \App\Models\RecordFile::with('record')
            ->whereHas('record', function ($q) use ($module) {
                $q->where('module_id', $module->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('modules.show', [
            'module'         => $module,
            'currentProject' => $module->project,
            'files'          => $files,
        ]);
    }

    /**
     * Update a module.
     */
    public function update(Request $request, Module $module)
    {
        $this->authorizeRole('editor');

        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
            'status'      => 'required|in:active,archived',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $module->update($validated);

        return redirect()->route('projects.modules.index', $module->project_id)
            ->with('success', '模組已更新。');
    }

}
