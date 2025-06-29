<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'type' => 'required|in:profile,cover',
        ]);

        try {
            $user = auth()->user();

            $folder = $validated['type'] === 'profile' ? 'profile_photos' : 'cover_photos';
            $path = $request->file('image')->store($folder, 'public');

            $user->images()->create([
                'path' => $path,
                'type' => $validated['type'],
            ]);

            return response()->json([
                'success' => 'Image uploaded successfully',
                'path' => Storage::url($path),
                'type' => $validated['type']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
public function setCurrent(Request $request)
{
    $request->validate([
        'image_id' => 'required|exists:images,id',
        'type' => 'required|in:profile,cover',
    ]);

    $user = auth()->user();

    $image = $user->images()
        ->where('id', $request->image_id)
        ->where('type', $request->type)
        ->first();

    if (!$image) {
        return response()->json(['error' => 'الصورة غير موجودة'], 404);
    }

    $image->update(['created_at' => now()]);

    return response()->json(['success' => 'تم تعيين الصورة كالحالية']);
}


    
}