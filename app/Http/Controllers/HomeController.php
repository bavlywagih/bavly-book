<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $user = Auth::user();
        $posts = Post::with('user', 'images')
        ->inRandomOrder()
        ->take(10)
        ->get();

        return view('home', compact('posts'  ,'user') );
    }

    public function loadPosts(Request $request)
{
    $skip = $request->input('skip', 0);

    $posts = Post::with('user', 'images')
        ->inRandomOrder()
        ->skip($skip)
        ->take(10)
        ->get();

    return response()->json($posts);
}


}
