<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        $projects = Project::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        $this->authorizeRole('editor');

        return view('projects.create');
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $this->authorizeRole('editor');

        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:10',
            'status'      => 'required|in:active,archived',
        ]);
        
        $validated['color'] = $validated['color'] ?? '#4f46e5';

        $validated['created_by'] = Auth::id();

        $project = Project::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', '專案已建立。');
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $project->load(['modules' => function ($query) {
            $query->orderBy('sort_order');
        }, 'files' => function ($query) {
            $query->orderBy('sort_order');
        }]);

        return view('projects.show', [
            'project'        => $project,
            'currentProject' => $project,
        ]);
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $this->authorizeRole('editor');

        return view('projects.edit', [
            'project'        => $project,
            'currentProject' => $project,
        ]);
    }

    /**
     * Update the specified project.
     * Note: no DELETE per spec §21 — use status=archived to retire.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorizeRole('editor');

        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:10',
            'status'      => 'required|in:active,archived',
        ]);
        
        $validated['color'] = $validated['color'] ?? $project->color ?? '#4f46e5';

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', '專案已更新。');
    }

    /**
     * AJAX: Get modules for a specific project
     */
    public function getModulesJson(Project $project)
    {
        return response()->json(
            $project->modules()->active()->orderBy('sort_order')->get(['id', 'name'])
        );
    }

    /**
     * Reorder projects.
     */
    public function reorder(Request $request)
    {
        $this->authorizeRole('editor');

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:projects,id',
        ]);

        foreach ($request->order as $index => $id) {
            Project::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['message' => '排序已更新']);
    }

}
