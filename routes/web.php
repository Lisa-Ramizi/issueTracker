<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\IssueActivityController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\IssueTagController;
use App\Http\Controllers\IssueUserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/projects');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('projects.index'))->name('dashboard');

    Route::resource('projects', ProjectController::class);
    Route::resource('projects.issues', IssueController::class)->shallow();
    Route::resource('tags', TagController::class)->only(['index', 'store']);
    Route::resource('issues.comments', CommentController::class)->only(['index', 'store', 'destroy'])->shallow();

    Route::post('issues/{issue}/tags/{tag}/attach', [IssueTagController::class, 'attach'])->name('issues.tags.attach');
    Route::delete('issues/{issue}/tags/{tag}/detach', [IssueTagController::class, 'detach'])->name('issues.tags.detach');

    Route::post('issues/{issue}/users/{user}/attach', [IssueUserController::class, 'attach'])->name('issues.users.attach');
    Route::delete('issues/{issue}/users/{user}/detach', [IssueUserController::class, 'detach'])->name('issues.users.detach');

    Route::patch('issues/{issue}/status', [IssueController::class, 'updateStatus'])->name('issues.status.update');
    Route::get('issues/{issue}/activities', [IssueActivityController::class, 'index'])->name('issues.activities.index');
});
