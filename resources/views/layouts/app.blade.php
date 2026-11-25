<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Task Manager')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900">
    <main class="mx-auto @yield('container-width', 'max-w-3xl') space-y-6 px-4 py-10">
        <header>
            <a href="{{ route('tasks.index') }}" class="text-3xl font-semibold">
                Task Manager
            </a>

            <p class="mt-2 text-slate-600">
                Choose a project, add tasks, and drag them into priority order.
            </p>
        </header>

        @if (session('success'))
            <div role="status" class="rounded-lg bg-green-100 p-4 text-green-900">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-lg bg-red-100 p-4 text-red-900">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
