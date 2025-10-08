<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\DiscussionController;
use App\Http\Controllers\Api\ReplyController;
use App\Http\Controllers\Api\ReportController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('course')->group(function() {
        Route::get('/', [CourseController::class, 'index']);
        Route::post('/', [CourseController::class, 'store']);
        Route::put('/{id}', [CourseController::class, 'update']);
        Route::delete('/{id}', [CourseController::class, 'destroy']);
        Route::post('/{id}/enroll', [CourseController::class, 'enroll']);
    });

    Route::prefix('materials')->group(function() {
        Route::post('/', [MaterialController::class, 'store']);
        Route::delete('/{id}', [MaterialController::class, 'destroy']);
        Route::post('/{id}/download', [MaterialController::class, 'download']);
    });

    Route::post('assignment', [AssignmentController::class, 'store']);

    Route::prefix('submissions')->group(function() {
        Route::post('/', [SubmissionController::class, 'store']);
        Route::put('/{id}/grade', [SubmissionController::class, 'grade']);
    });

    Route::post('discussions', [DiscussionController::class, 'store']);

    Route::post('discussions/{id}/replies', [ReplyController::class, 'store']);

    Route::prefix('reports')->group(function() {
        Route::get('/courses', [ReportController::class, 'courses']);
        Route::get('/assignments', [ReportController::class, 'assignments']);
        Route::get('/students/{id}', [ReportController::class, 'student']);
    });

    Route::post('logout', [AuthController::class, 'logout']);
});
