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
        // هنا يمكنك إعادة توجيه المستخدم إلى صفحة تسجيل الدخول أو عرض محتوى عام
            return redirect()->route('login'); // مثال: إعادة توجيه لصفحة الدخول
        }



        // بوستات غير مشاهدة بشكل عشوائي
        $posts = Post::selectRaw('posts.id, posts.user_id, posts.body, posts.created_at, COUNT(loves.id) as loves_count')
            ->leftJoin('post_views as pv', function ($join) use ($user) {
                $join->on('pv.post_id', '=', 'posts.id')
                    ->where('pv.user_id', $user->id);
            })
            ->leftJoin('loves', 'loves.post_id', '=', 'posts.id')
            ->with([
                'user.currentProfilePhoto',
                'images',
                'loves.user.currentProfilePhoto',
            ])
            ->groupBy('posts.id', 'posts.user_id', 'posts.body', 'posts.created_at')
            ->whereNull('pv.id')
            ->inRandomOrder() // ترتيب عشوائي للبوستات غير المشاهدة
            ->take(10)
            ->get()
            ->map(function ($post) use ($user) {
                $post->seen_by_user = false;
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
    $skip = $request->input('skip', 0);
    $limit = 10;
    $user = Auth::user();

    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // أولًا: بوستات جديدة لم تُشاهد بعد
    $newPosts = Post::whereDoesntHave('views', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with([
            'user.currentProfilePhoto',
            'images',
            'loves.user.currentProfilePhoto'
        ])
        ->inRandomOrder()
        ->take($limit)
        ->get();

    // لو فيه بوستات جديدة كفاية، رجعها فقط
    if ($newPosts->count() > 0) {
        $posts = $newPosts;
    } else {
        // مفيش بوستات جديدة؟ هات من البوستات القديمة اللي شافها
        $posts = Post::whereHas('views', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with([
                'user.currentProfilePhoto',
                'images',
                'loves.user.currentProfilePhoto'
            ])
            ->withCount('loves')
            ->orderByDesc('loves_count')
            ->orderByDesc('created_at')
            ->skip($skip)
            ->take($limit)
            ->get();
    }

    // نفس الماب اللي بتستخدمه لمعالجة البيانات
    $posts = $posts->map(function ($post) use ($user) {
        $post->seen_by_user = $post->views->contains('user_id', $user->id);
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

        // سجّل إن المستخدم شاف البوست (لو مش موجود أصلًا)
        PostView::firstOrCreate([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        return $post;
    });

    return response()->json($posts);
}


}
