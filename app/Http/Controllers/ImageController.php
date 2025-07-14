<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
public function upload(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'type' => 'required|in:cover,profile',
    ]);

    $user = auth()->user();

    $user->images()->where('type', $request->type)->update(['is_current' => false]);

    $path = $request->file('image')->store($request->type . '_photos', 'public');

    $user->images()->create([
        'path' => $path,
        'type' => $request->type,
        'is_current' => true,
    ]);

    return response()->json(['success' => 'تم رفع الصورة بنجاح']);
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

    $user->images()->where('type', $request->type)->update(['is_current' => false]);

    $image->is_current = true;
    $image->save();

    return response()->json(['success' => 'تم تعيين الصورة كالحالية']);
}



    
}