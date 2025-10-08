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
        $this->authorize('create', Discussion::class);

        $course = Course::findOrFail($request->course_id);

        $disc = Discussion::create([
            'course_id' => $course->id,
            'user_id' => $request->user()->id,
            'content' => $request->content
        ]);

        broadcast(new ChatMessageSent($disc))->toOthers();
        return response()->json([
            'success' => true,
            'message' => 'Diskusi berhasil ditambahkan',
            'data' => $disc
        ], 201);
    }
}
