<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderTasksRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReorderTasksController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        ReorderTasksRequest $request,
        Project $project,
    ): JsonResponse {
        $taskIds = array_map(
            static fn ($id): int => (int) $id,
            $request->validated('task_ids'),
        );

        DB::transaction(function () use ($project, $taskIds): void {
            Project::whereKey($project->id)->lockForUpdate()->firstOrFail();

            $tasks = $project->tasks()
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $currentIds = $tasks->keys()->sort()->values()->all();
            $submittedIds = collect($taskIds)->sort()->values()->all();

            // Reject foreign, missing, or stale task IDs before changing priorities.
            if ($currentIds !== $submittedIds) {
                throw ValidationException::withMessages([
                    'task_ids' => 'Submit every task in this project exactly once. '
                        .'Refresh the page and try again.',
                ]);
            }

            foreach ($taskIds as $position => $taskId) {
                $tasks->get($taskId)->update([
                    'priority' => $position + 1,
                ]);
            }
        });

        return response()->json([
            'message' => 'Task order saved.',
        ]);
    }
}
