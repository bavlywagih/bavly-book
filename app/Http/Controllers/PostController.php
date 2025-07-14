<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
{
    try {
        $request->validate([
            'body' => 'nullable|string',
            'images.*' => 'image|max:2048',
        ]);
        if (!$request->filled('body') && !$request->hasFile('images')) {
            return response()->json([
                'error' => 'يجب كتابة نص أو رفع صورة واحدة على الأقل.'
            ], 422);
        }

        $post = auth()->user()->posts()->create([
            'body' => $request->input('body'),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('post_images', 'public');
                $post->images()->create([
                    'path' => $path,
                    'user_id' => auth()->id(),
                ]);
            }
        }

        $post->load([
            'user.currentProfilePhoto',
            'images',
            'loves.user.currentProfilePhoto',
        ]);

        $post->user_loved = $post->loves->contains('user_id', auth()->id());
        $post->created_at_diff = $post->created_at->diffForHumans();

        return response()->json($post);

    } catch (\Throwable $e) {
        \Log::error($e);
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
}



}
