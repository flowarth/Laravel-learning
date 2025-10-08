<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeRequest;
use App\Http\Requests\SubmissionRequest;
use App\Models\Assignment;
use App\Models\Submission;
use App\Notifications\GradeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class SubmissionController extends Controller
{
    public function store(SubmissionRequest $request)
    {
        $this->authorize('create', Submission::class);

        $path = $request->file('file')->store('submissions', 'public');

        $assignment = Assignment::findOrFail($request->assignment_id);

        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $request->user()->id,
            'file_path' => $path,
        ]);

        return response()->json($submission, 201);
    }

    public function grade(GradeRequest $request, $id)
    {
        $submission = Submission::findOrFail($id);

        $this->authorize('grade', $submission);

        $submission->score = $request->score;
        $submission->save();

        $submission->load('assignment', 'student');

        Notification::route('mail', $submission->student->email)->notify(new GradeNotification($submission));

        return response()->json($submission);
    }
}
