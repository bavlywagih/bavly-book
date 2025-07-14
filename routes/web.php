<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentLoveController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LoveController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Models\PostView;
use Illuminate\Support\Facades\Route;



Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'login_post'])->name('login_post');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');




Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/upload-image', [ImageController::class, 'upload'])->name('image.upload');
    Route::get('/profile/images/{type}', [ProfileController::class, 'getImages']);
    Route::post('/profile/images/set-current', [ProfileController::class, 'setCurrentImage']);
    Route::delete('/profile/images/delete/{id}', [ProfileController::class, 'deleteImage']);
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/load-posts', [HomeController::class, 'loadPosts']);
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/posts/{post}/view', function (\App\Models\Post $post) {
        PostView::firstOrCreate([
            'user_id' => auth()->id(),
            'post_id' => $post->id,
        ]);
    });
    Route::post('/comments/{comment}/love', [CommentLoveController::class, 'toggle']);
    Route::get('/comments/{comment}/loves', function (\App\Models\Comment $comment) {
        $comment->load('loves.user.currentProfilePhoto');
        return $comment->loves;
    });

    Route::get('/comments/{comment}/html', function (\App\Models\Comment $comment) {
        $comment->load('user', 'images', 'loves', 'replies.user', 'replies.images', 'replies.loves');
        return view('components.comment', ['comment' => $comment])->render();
    });
    Route::get('/posts/{post}/comments/html', function (\App\Models\Post $post) {
        $post->load('comments.user.currentProfilePhoto', 'comments.images', 'comments.loves', 'comments.replies.user.currentProfilePhoto', 'comments.replies.images', 'comments.replies.loves');
        return view('partials.comments', ['post' => $post])->render();
    });



    // Route::post('/love-toggle', [LoveController::class, 'toggle'])->name('love.toggle');
    Route::post('/posts/{post}/love', [LoveController::class, 'toggleLove']);
    Route::get('/profile/images/{type}', function ($type) {
        $user = auth()->user();

        if (!in_array($type, ['profile', 'cover'])) {
            return response()->json([], 400);
        }

        $images = $user->images()->where('type', $type)->orderByDesc('created_at')->get();

        return response()->json($images);
    });

});




