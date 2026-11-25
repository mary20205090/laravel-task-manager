<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReorderTasksController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaskController::class, 'index'])
    ->name('tasks.index');

Route::post('/projects', [ProjectController::class, 'store'])
    ->name('projects.store');

Route::post('/tasks', [TaskController::class, 'store'])
    ->name('tasks.store');

Route::scopeBindings()->group(function (): void {
    Route::put('/projects/{project}/tasks/reorder', ReorderTasksController::class)
        ->name('projects.tasks.reorder');

    Route::resource('projects.tasks', TaskController::class)
        ->only(['edit', 'update', 'destroy']);
});
