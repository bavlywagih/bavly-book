<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'body' => 'nullable|string',
        'images.*' => 'image|mimes:jpg,png,jpeg|max:2048',
    ]);

    $post = auth()->user()->posts()->create([
        'body' => $request->body,
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $imageFile) {
            $path = $imageFile->store('post_images', 'public');

            $post->images()->create([
                'user_id' => auth()->id(),
                'path' => $path,
                'type' => 'post',
            ]);
        }
    }

    return redirect()->back()->with('success', 'تم نشر المنشور بنجاح');
}

}
