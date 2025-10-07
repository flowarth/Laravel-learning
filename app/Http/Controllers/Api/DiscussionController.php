<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Discussion;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'content' => 'required'
        ]);

        $course = Course::findOrFail($request->course_id);

        if ($request->user()->role === 'student' && !$course->students->contains($request->user()->id)) {
            return response()->json([
                'message' => 'Must be enrolled to discuss'
            ], 403);
        }
        $disc = Discussion::create([
            'course_id' => $course->id,
            'user_id' => $request->user()->id,
            'content' => $request->content
        ]);

        broadcast(new ChatMessageSent($disc))->toOthers();
        return response()->json($disc, 201);
    }
}
