<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLove;
use Illuminate\Http\Request;

class CommentLoveController extends Controller
{
    public function toggle(Comment $comment)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $loved = $comment->loves()->where('user_id', $user->id)->exists();

        if ($loved) {
            $comment->loves()->where('user_id', $user->id)->delete();
        } else {
            $comment->loves()->create([
                'user_id' => $user->id
            ]);
        }

        return response()->json([
            'count' => $comment->loves()->count(),
            'loved' => !$loved
        ]);
    }
}
