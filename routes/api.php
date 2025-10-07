<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;

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
});
