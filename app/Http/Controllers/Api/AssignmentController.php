<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignmentRequest;
use App\Models\Assignment;
use App\Models\Course;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Support\Facades\Notification;

class AssignmentController extends Controller
{
    public function store(AssignmentRequest $request)
    {
        $this->authorize('create', Assignment::class);

        $course = Course::findOrFail($request->course_id);

        $assignment = Assignment::create($request->validated());
        $assignment->load('course');

        $students = $course->students;
        foreach ($students as $student) {
            \Illuminate\Support\defer(fn() => Notification::route('mail', $student->email)->notify(new NewAssignmentNotification($assignment)));
        }

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dibuat',
            'data' => $assignment
        ], 201);
    }
}
