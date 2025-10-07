<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\SubmissionController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('course', [CourseController::class, 'index']);
    Route::post('course', [CourseController::class, 'store']);
    Route::get('course/{id}', [CourseController::class, 'show']);
    Route::put('course/{id}', [CourseController::class, 'update']);
    Route::delete('course/{id}', [CourseController::class, 'destroy']);
    Route::post('course/{id}/enroll', [CourseController::class, 'enroll']);
    Route::get('my-courses', [CourseController::class, 'myCourses']);

    Route::get('materials/{id}', [MaterialController::class, 'index']);
    Route::post('materials', [MaterialController::class, 'store']);
    Route::delete('materials/{id}', [MaterialController::class, 'destroy']);
    Route::post('materials/{id}/download', [MaterialController::class, 'download']);

    Route::post('assignment', [AssignmentController::class, 'store']);

    Route::post('submissions', [SubmissionController::class, 'store']);
    Route::put('submissions/{id}/grade', [SubmissionController::class, 'grade']);
});
