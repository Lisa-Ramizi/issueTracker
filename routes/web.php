<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\IssueTagController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/projects');

Route::resource('projects', ProjectController::class);
Route::resource('projects.issues', IssueController::class)->shallow();
Route::resource('tags', TagController::class)->only(['index', 'store']);
Route::resource('issues.comments', CommentController::class)->only(['index', 'store'])->shallow();

Route::post('issues/{issue}/tags/{tag}/attach', [IssueTagController::class, 'attach'])->name('issues.tags.attach');
Route::delete('issues/{issue}/tags/{tag}/detach', [IssueTagController::class, 'detach'])->name('issues.tags.detach');
