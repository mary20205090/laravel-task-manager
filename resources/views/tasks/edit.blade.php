@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
    <section class="space-y-5 rounded-xl bg-white p-6 shadow-sm">
        <div>
            <h1 class="text-xl font-semibold">Edit task</h1>
            <p class="text-sm text-slate-600">{{ $project->name }}</p>
        </div>

        <form
            method="POST"
            action="{{ route('projects.tasks.update', [$project, $task]) }}"
            class="space-y-4"
        >
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label for="task-name" class="block font-medium">Task name</label>

                <input
                    id="task-name"
                    name="name"
                    value="{{ old('name', $task->name) }}"
                    maxlength="255"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2"
                >
            </div>

            <div class="flex items-center gap-4">
                <button class="rounded-lg bg-slate-900 px-4 py-2 text-white">
                    Save changes
                </button>

                <a
                    href="{{ route('tasks.index', ['project_id' => $project->id]) }}"
                    class="text-slate-600 hover:underline"
                >Cancel</a>
            </div>
        </form>
    </section>
@endsection
