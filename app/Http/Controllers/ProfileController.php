<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
public function show()
{
    $user = Auth::user();

    // تعيين أحدث صورة كـ current إذا لا يوجد واحدة مفعّلة
    foreach (['cover', 'profile'] as $type) {
        $hasCurrent = $user->images()->where('type', $type)->where('is_current', true)->exists();
        if (!$hasCurrent) {
            $latest = $user->images()->where('type', $type)->latest()->first();
            if ($latest) {
                $latest->is_current = true;
                $latest->save();
            }
        }
    }

    return view('profile', compact('user'));
}


public function getImages($type)
    {
        return auth()->user()->images()
            ->where('type', $type)
            ->orderByDesc('created_at')
            ->get(['id', 'path'])
            ->map(function ($img) {
                return [
                    'id' => $img->id,
                    'path' => $img->path,
                    'url' => asset('storage/' . $img->path), // هذا المهم
                ];
            });
    }


public function setCurrentImage(Request $request)
{
    $request->validate([
        'image_id' => 'required|exists:images,id',
        'type' => 'required|in:cover,profile',
    ]);

    $image = Image::where('id', $request->image_id)
        ->where('user_id', auth()->id())
        ->where('type', $request->type)
        ->first();

    if (!$image) {
        return response()->json(['error' => 'الصورة غير موجودة أو غير تابعة للمستخدم'], 404);
    }

    // جعل كل الصور السابقة غير حالية
    Image::where('user_id', auth()->id())
        ->where('type', $request->type)
        ->update(['is_current' => false]);

    // تعيين هذه الصورة كـ الحالية
    $image->is_current = true;
    $image->created_at = now(); // ✅ إضافة هذا السطر
    $image->save();

    return response()->json(['success' => 'تم تعيين الصورة كالحالية']);
}


// ProfileController.php
public function update(Request $request)
{
    $user = auth()->user();
    dd(

        sprintf(
            $request->birthday
        )
        );
    $request->validate([
        'first_name' => 'required|string|max:50',
        'last_name'  => 'required|string|max:50',
        'mobile'     => 'nullable|string|max:20',
        'birthday'   => 'nullable|date',
        'gender'     => 'nullable|in:male,female',
    ]);

    $user->update($request->only('first_name', 'last_name', 'mobile', 'birthday', 'gender'));

    return redirect('/profile?tab=about')->with('success', 'تم تحديث البيانات بنجاح');
}


    public function deleteImage($id)
    {
        $image = auth()->user()->images()->findOrFail($id);
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json(['success' => true]);
    }

}

