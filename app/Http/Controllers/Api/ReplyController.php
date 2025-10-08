<?php

namespace App\Http\Controllers\Api;

use App\Events\ReplyMessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReplyRequest;
use App\Models\Reply;

class ReplyController extends Controller
{
    public function store(ReplyRequest $request, $discussionId)
    {
        $reply = Reply::create([
            'discussion_id' => $discussionId,
            'user_id' => $request->user()->id,
            'content' => $request->content
        ]);
        broadcast(new ReplyMessageSent($reply))->toOthers();
        return response()->json($reply, 201);
    }
}
