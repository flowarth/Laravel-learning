<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\DiscussionRequest;
use App\Models\Course;
use App\Models\Discussion;

class DiscussionController extends Controller
{
    public function store(DiscussionRequest $request)
    {
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
