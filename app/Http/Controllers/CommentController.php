<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'body' => 'nullable|string',
        'post_id' => 'required|exists:posts,id',
        'parent_id' => 'nullable|exists:comments,id',
        'images.*' => 'nullable|image|max:2048',
    ]);

    $comment = Comment::create([
        'user_id' => auth()->id(),
        'post_id' => $request->post_id,
        'parent_id' => $request->parent_id,
        'body' => $request->body,
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('comment_images', 'public');
            $comment->images()->create(['path' => $path]);
        }
    }

    $comment->load('user', 'images', 'replies');

    return response()->json($comment);
}

}
