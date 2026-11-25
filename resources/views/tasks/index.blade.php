@extends('layouts.app')

@section('container-width', 'max-w-6xl')

@section('content')
    <div class="grid items-start gap-6 lg:grid-cols-[18rem_minmax(0,1fr)]">
        <section class="space-y-5 rounded-xl bg-white p-5 shadow-sm">
            <div>
                <h2 class="text-xl font-semibold">Projects</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Select a project to view its tasks.
                </p>
            </div>

            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th scope="col" class="px-2 py-3 font-medium">Project</th>
                        <th scope="col" class="px-2 py-3 text-right font-medium">Tasks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr @class([
                            'border-b border-slate-100',
                            'bg-blue-50' => $selectedProject?->id === $project->id,
                        ])>
                            <td class="px-2 py-3">
                                <a
                                    href="{{ route('tasks.index', ['project_id' => $project->id]) }}"
                                    class="break-words font-medium text-blue-700 hover:underline"
                                    @if ($selectedProject?->id === $project->id)
                                        aria-current="page"
                                    @endif
                                >
                                    {{ $project->name }}
                                </a>
                            </td>
                            <td class="px-2 py-3 text-right text-slate-600">
                                {{ $project->tasks_count }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="py-5 text-slate-500">
                                No projects yet. Create your first one below.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <form method="POST" action="{{ route('projects.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="_form" value="project">

                <label for="project-name" class="block text-sm font-medium">
                    New project
                </label>

                <input
                    id="project-name"
                    name="name"
                    placeholder="e.g. Website Launch"
                    value="{{ old('_form') === 'project' ? old('name') : '' }}"
                    maxlength="255"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2"
                >

                <button class="w-full rounded-lg border border-slate-300 px-4 py-2">
                    Create project
                </button>
            </form>
        </section>

        <section class="min-w-0 space-y-6 rounded-xl bg-white p-6 shadow-sm">
            @if ($selectedProject)
                <div class="space-y-3">
                    <h1 class="text-xl font-semibold">Tasks</h1>

                    <form method="GET" action="{{ route('tasks.index') }}" class="space-y-2">
                        <label for="project-filter" class="block text-sm font-medium">
                            Show tasks for
                        </label>

                        <div class="flex gap-2">
                            <select
                                id="project-filter"
                                name="project_id"
                                class="min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2"
                            >
                                @foreach ($projects as $project)
                                    <option
                                        value="{{ $project->id }}"
                                        @selected($selectedProject->id === $project->id)
                                    >
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button
                                type="submit"
                                data-filter-submit
                                class="rounded-lg border border-slate-300 px-4 py-2" >
                                View
                            </button>
                        </div>
                    </form>
                </div>

                <form
                    method="POST"
                    action="{{ route('tasks.store') }}"
                    class="space-y-3 rounded-lg bg-slate-50 p-4"
                >
                    @csrf
                    <input type="hidden" name="_form" value="task">

                    <h2 class="font-semibold">Add a task</h2>

                    <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_12rem_auto] md:items-end">
                        <div class="min-w-0 space-y-2">
                            <label for="task-name" class="block text-sm font-medium">
                                Task name
                            </label>

                            <input
                                id="task-name"
                                name="name"
                                placeholder="e.g. Review homepage"
                                value="{{ old('_form') === 'task' ? old('name') : '' }}"
                                maxlength="255"
                                required
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2"
                            >
                        </div>

                        <div class="min-w-0 space-y-2">
                            <label for="task-project" class="block text-sm font-medium">
                                Add to project
                            </label>

                            <select
                                id="task-project"
                                name="project_id"
                                required
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2"
                            >
                                @foreach ($projects as $project)
                                    <option
                                        value="{{ $project->id }}"
                                        @selected((int) old('project_id', $selectedProject->id) === $project->id)
                                    >
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button class="rounded-lg bg-slate-900 px-4 py-2 text-white">
                            Add task
                        </button>
                    </div>
                </form>

                <div>
                    <h2 class="font-semibold">{{ $selectedProject->name }}</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Drag using the handle. Priority 1 appears at the top.
                    </p>
                    <p id="order-status" role="status" class="mt-2 text-sm text-slate-600"></p>
                </div>

                <ul
                    id="task-list"
                    data-reorder-url="{{ route('projects.tasks.reorder', $selectedProject) }}"
                    class="space-y-3"
                >
                    @forelse ($tasks as $task)
                        <li
                            data-task-id="{{ $task->id }}"
                            class="flex flex-wrap items-center gap-3 rounded-lg border border-slate-200 p-4"
                        >
                            <span
                                class="drag-handle cursor-grab touch-none select-none text-xl text-slate-500"
                                title="Drag to reorder"
                                aria-hidden="true"
                            >⠿</span>

                            <span
                                data-priority
                                class="rounded bg-slate-100 px-2 py-1 text-sm font-semibold"
                            >#{{ $task->priority }}</span>

                            <div class="min-w-0 flex-1">
                                <p class="break-words font-medium">{{ $task->name }}</p>
                                <p class="text-xs text-slate-500">
                                    Created {{ $task->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>

                            <a
                                href="{{ route('projects.tasks.edit', [$selectedProject, $task]) }}"
                                class="text-sm font-medium text-blue-700 hover:underline"
                            >Edit</a>

                            <form
                                method="POST"
                                action="{{ route('projects.tasks.destroy', [$selectedProject, $task]) }}"
                                data-delete-task
                            >
                                @csrf
                                @method('DELETE')

                                <button class="text-sm font-medium text-red-700 hover:underline">
                                    Delete
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="rounded-lg border border-dashed border-slate-300 p-8 text-center">
                            <p class="font-medium">No tasks in this project yet.</p>
                            <p class="mt-1 text-sm text-slate-500">
                                Add a task above to get started.
                            </p>
                        </li>
                    @endforelse
                </ul>
            @else
                <div class="py-12 text-center">
                    <h1 class="text-xl font-semibold">Start with a project</h1>
                    <p class="mt-2 text-slate-600">
                        Create your first project to start adding and organizing tasks.
                    </p>
                </div>
            @endif
        </section>
    </div>
@endsection
