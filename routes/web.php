<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



Route::middleware('auth')->get('/', [HomeController::class, 'home'])->name('home');
Route::middleware('guest')->get('/login', [HomeController::class, 'home'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');




Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/upload-image', [ImageController::class, 'upload'])->name('image.upload');
    Route::get('/profile/images/{type}', [ProfileController::class, 'getImages']);
    Route::post('/profile/images/set-current', [ProfileController::class, 'setCurrentImage']);
    Route::delete('/profile/images/delete/{id}', [ProfileController::class, 'deleteImage']);
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');


});


Route::get('/profile/images/{type}', function ($type) {
    $user = auth()->user();

    if (!in_array($type, ['profile', 'cover'])) {
        return response()->json([], 400);
    }

    $images = $user->images()->where('type', $type)->orderByDesc('created_at')->get();

    return response()->json($images);
});

