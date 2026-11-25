<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ]);

        $projects = Project::withCount('tasks')->orderBy('name')->get();

        $selectedProject = isset($validated['project_id'])
            ? $projects->firstWhere('id', (int) $validated['project_id'])
            : $projects->first();

        $tasks = $selectedProject?->tasks()
            ->orderBy('priority')
            ->orderBy('id')
            ->get() ?? collect();

        return view('tasks.index', compact(
            'projects',
            'selectedProject',
            'tasks',
        ));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $project = DB::transaction(function () use ($request): Project {
            // Lock the chosen project before assigning its next priority.
            $project = Project::whereKey($request->validated('project_id'))
                ->lockForUpdate()
                ->firstOrFail();

            $priority = ($project->tasks()->max('priority') ?? 0) + 1;

            $project->tasks()->create([
                'name' => $request->validated('name'),
                'priority' => $priority,
            ]);

            return $project;
        });

        return to_route('tasks.index', ['project_id' => $project->id])
            ->with('success', "Task added to {$project->name}.");
    }

    public function edit(Project $project, Task $task): View
    {
        return view('tasks.edit', compact('project', 'task'));
    }

    public function update(
        SaveTaskRequest $request,
        Project $project,
        Task $task,
    ): RedirectResponse {
        $task->update($request->validated());

        return to_route('tasks.index', ['project_id' => $project->id])
            ->with('success', 'Task updated.');
    }

    public function destroy(Project $project, Task $task): RedirectResponse
    {
        DB::transaction(function () use ($project, $task): void {
            Project::whereKey($project->id)->lockForUpdate()->firstOrFail();

            // Reload after locking: another request may have changed its priority.
            $task = $project->tasks()->findOrFail($task->id);
            $priority = $task->priority;

            $task->delete();

            $project->tasks()
                ->where('priority', '>', $priority)
                ->decrement('priority');
        });

        return to_route('tasks.index', ['project_id' => $project->id])
            ->with('success', 'Task deleted.');
    }
}
