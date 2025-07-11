<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\Love;
class LoveController extends Controller
{

public function toggleLove(Request $request, Post $post)
{
    $user = auth()->user();

    $love = Love::where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->first();

    $loved = false;

    if ($love) {
        $love->delete();
    } else {
        Love::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
        $loved = true;
    }

    return response()->json([
        'count' => $post->loves()->count(),
        'loved' => $loved
    ]);
}


}
