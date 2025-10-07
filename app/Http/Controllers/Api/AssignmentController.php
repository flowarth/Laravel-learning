<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class AssignmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date|after:now',
        ]);

        $course = Course::findOrFail($validated['course_id']);

        if ($course->lecturer_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk membuat tugas di mata kuliah ini'
            ], 403);
        }

        $assignment = Assignment::create($validated);
        $assignment->load('course');

        $students = $course->students;
        foreach ($students as $student) {
            Notification::route('mail', $student->email)->notify(new NewAssignmentNotification($assignment));
        }

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dibuat',
            'data' => $assignment
        ], 201);
    }
}
