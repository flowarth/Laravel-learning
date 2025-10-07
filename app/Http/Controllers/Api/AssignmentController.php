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
    public function store(AssignmentRequest $request, ?Course $course = null)
    {
        if ($course->lecturer_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk membuat tugas di mata kuliah ini'
            ], 403);
        }

        $assignment = Assignment::create($request);
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
