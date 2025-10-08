<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function courses()
    {
        $data = Course::withCount('students')->get()->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'students_count' => $c->students_count
            ];
        });
        return response()->json($data);
    }

    public function assignments()
    {
        $total = Assignment::count();
        $graded = Submission::whereNotNull('score')->count();
        $ungraded = Submission::whereNull('score')->count();

        return response()->json([
            'total_assignments' => $total,
            'graded_submissions' => $graded,
            'ungraded_submissions' => $ungraded
        ]);
    }

    public function student($id)
    {
        $student = User::with(['submissions.assignment.course'])->findOrFail($id);
        $avg = $student->submissions()->whereNotNull('score')->avg('score');

        return response()->json([
            'student' => $student,
            'average_score' => $avg]);
    }
}
