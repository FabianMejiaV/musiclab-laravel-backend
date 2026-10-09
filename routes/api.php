<?php

use App\Http\Controllers\Api\ProjectContentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProjectsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('users.projects', ProjectsController::class);

    Route::get('users/{user}/projects/{project}/content', [ProjectContentController::class, 'show']);
    Route::patch('users/{user}/projects/{project}/content', [ProjectContentController::class, 'update']);
});
