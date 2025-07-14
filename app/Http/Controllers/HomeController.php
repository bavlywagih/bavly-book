<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function login()
    {
        return view('home');
    }
public function home()
{
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    $viewedPostIds = PostView::where('user_id', $user->id)->pluck('post_id')->toArray();

    $posts = Post::with([
            'user.currentProfilePhoto',
            'images',
            'loves.user.currentProfilePhoto',
            'views'
        ])
        ->inRandomOrder()
        ->take(10)
        ->get();

    $posts = $posts->map(function ($post) use ($user) {
        if (!$post->views->contains('user_id', $user->id)) {
            PostView::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
        }

        $post->seen_by_user = $post->views->contains('user_id', $user->id);
        $post->view_count = $post->views->count();
        $post->created_at_diff = $post->created_at->diffForHumans();
        $post->user_loved = $post->loves->contains('user_id', $user->id);

        $post->loves = $post->loves->map(function ($love) {
            return [
                'id' => $love->id,
                'user_id' => $love->user_id,
                'user' => [
                    'first_name' => $love->user->first_name,
                    'last_name' => $love->user->last_name,
                    'photo' => $love->user->currentProfilePhoto
                        ? asset('storage/' . $love->user->currentProfilePhoto->path)
                        : asset('images/default-profile.jpg'),
                ],
            ];
        });

        return $post;
    });

    return view('home', compact('posts', 'user'));
}

public function loadPosts(Request $request)
{
    $user = Auth::user();


    $posts = Post::with([
            'user.currentProfilePhoto',
            'images',
            'loves.user.currentProfilePhoto',
            'views'
        ])
        ->withCount('loves')
        ->inRandomOrder()
        ->take(15)
        ->get();

    $posts->map(function ($post) use ($user) {
        $post->seen_by_user = $post->views->contains('user_id', $user->id);
        $post->created_at_diff = $post->created_at->diffForHumans();
        $post->user_loved = $post->loves->contains('user_id', $user->id);
        $post->view_count = $post->views->count();
        return $post;
    });

    return response()->json($posts);
}




}
