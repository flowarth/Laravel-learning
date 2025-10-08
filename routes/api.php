<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\DiscussionController;
use App\Http\Controllers\Api\ReplyController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('course')->group(function() {
        Route::get('/', [CourseController::class, 'index']);
        Route::post('/', [CourseController::class, 'store']);
        Route::put('/{course}', [CourseController::class, 'update']);
        Route::delete('/{course}', [CourseController::class, 'destroy']);
        Route::post('/{course}/enroll', [CourseController::class, 'enroll']);
    });

    Route::prefix('materials')->group(function() {
        Route::post('/', [MaterialController::class, 'store']);
        Route::delete('/{materials}', [MaterialController::class, 'destroy']);
        Route::post('/{id}/download', [MaterialController::class, 'download']);
    });

    Route::post('assignment', [AssignmentController::class, 'store']);

    Route::prefix('submissions')->group(function() {
        Route::post('/', [SubmissionController::class, 'store']);
        Route::put('/{id}/grade', [SubmissionController::class, 'grade']);
    });

    Route::post('discussions', [DiscussionController::class, 'store']);

    Route::post('discussions/{id}/replies', [ReplyController::class, 'store']);

    Route::post('logout', [AuthController::class, 'logout']);
});
