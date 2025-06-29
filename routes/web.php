<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



Route::get('/', [HomeController::class, 'home'])->name('home');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    Route::post('/upload-image', [ImageController::class, 'upload'])->name('image.upload');
});


Route::get('/profile/images/{type}', function ($type) {
    $user = auth()->user();

    if (!in_array($type, ['profile', 'cover'])) {
        return response()->json([], 400);
    }

    $images = $user->images()->where('type', $type)->orderByDesc('created_at')->get();

    return response()->json($images);
});

Route::post('/profile/images/set-current', [ImageController::class, 'setCurrent'])->name('image.setCurrent');
