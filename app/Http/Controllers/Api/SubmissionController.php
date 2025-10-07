<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use App\Notifications\GradeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class SubmissionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'file' => 'required|file'
        ]);

        $assignment = Assignment::findOrFail($request->assignment_id);

        if ($request->user()->role !== 'mahasiswa') {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk ini'
            ], 403);
        }

        $path = $request->file('file')->store('submissions', 'public');

        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $request->user()->id,
            'file_path' => $path,
        ]);

        return response()->json($submission, 201);
    }

    public function grade(Request $request, $id)
    {
        $request->validate([
            'score' => 'required|integer|min:0|max:100'
        ]);

        $submission = Submission::findOrFail($id);
        $assignment = $submission->assignment->course;

        if ($request->user()->id !== $assignment->lecturer_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $submission->score = $request->score;
        $submission->save();

        $submission->load('assignment', 'student');

        Notification::route('mail', $submission->student->email)->notify(new GradeNotification($submission));

        return response()->json($submission);
    }
}
