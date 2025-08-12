<?php

use App\Http\Controllers\Team\Todo\TodoController;
use Illuminate\Support\Facades\Route;



Route::get('/todo', [TodoController::class, 'index'])->name('todo.index');
Route::get('/todos/get', [TodoController::class, 'getTodos'])->name('todos.get');
Route::post('/todos/store', [TodoController::class, 'store'])->name('todos.store');
Route::post('/todos/{id}', [TodoController::class, 'update'])->name('todos.update');
Route::delete('/todos/{id}', [TodoController::class, 'destroy'])->name('todos.destroy');
Route::post('/todos/{id}/update-status', [TodoController::class, 'updateStatus']);
Route::post('/todos/tasks/{id}/update-status', [TodoController::class, 'updateTaskStatus'])->name('tasks.update-status');
