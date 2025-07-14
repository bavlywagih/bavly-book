عايز اعمل نظام كومنتات علي البوستات وفيها ريبلاي لا نهائي وعايز اعمل نظام رفع صور في الكومنتات
وموجود في الريبلاي 
وعند عمل ريبلاي عند الضغط علي ريبلاي يتعمل في @اسم الشخص 
ويبقي في لاف في ال كومنت بردو 
الاكواد اللي معايا حاليا لارافيل هديهالك دلوقتي <?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'password',
        'birthday',
        'gender',
    ];

public function images()
{
    return $this->hasMany(Image::class);
}

public function posts()
{
    return $this->hasMany(Post::class);
}
public function profilePhotos()
{
    return $this->images()->where('type', 'profile')->orderByDesc('created_at');
}
public function currentCoverPhoto()
{
    return $this->hasOne(Image::class)->where('type', 'cover')->where('is_current', true);
}
public function currentProfilePhoto()
{
    return $this->hasOne(Image::class)->where('type', 'profile')->where('is_current', true);
}
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Love;
class Post extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'body'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function images()
    {
        return $this->hasMany(Image::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }
    public function loves()
    {
        return $this->hasMany(Love::class);
    }
    public function views()
{
    return $this->hasMany(PostView::class);
}
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Love extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'post_id'];
    protected $table = 'loves';
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Image extends Model
{
    use HasFactory;
protected $fillable = ['user_id', 'path', 'type', 'is_current'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function post()
{
    return $this->belongsTo(Post::class);
}
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CommentImage extends Model
{
        use HasFactory;
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Comment extends Model
{
    use HasFactory;
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
    public function images()
    {
        return $this->hasMany(CommentImage::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
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
<?php
namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\Love;
class LoveController extends Controller
{
public function toggleLove(Request $request, Post $post)
{
    $user = auth()->user();
    $love = Love::where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->first();
    $loved = false;
    if ($love) {
        $love->delete();
    } else {
        Love::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
        $loved = true;
    }
    return response()->json([
        'count' => $post->loves()->count(),
        'loved' => $loved
    ]);
}
}
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
        return redirect()->route('login');
    }
    $viewedPostIds = PostView::where('user_id', $user->id)->pluck('post_id')->toArray();
    $posts = Post::with([
            'user.currentProfilePhoto',
            'images',
            'loves.user.currentProfilePhoto',
            'views'
        ])
        ->inRandomOrder()
        ->take(10)
        ->get();
    $posts = $posts->map(function ($post) use ($user) {
        if (!$post->views->contains('user_id', $user->id)) {
            PostView::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
        }
        $post->seen_by_user = $post->views->contains('user_id', $user->id);
        $post->view_count = $post->views->count();
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
                        : asset('image/default-user-photo.png'),
                ],
            ];
        });
        return $post;
    });
    return view('home', compact('posts', 'user'));
}
public function loadPosts(Request $request)
{
    $user = Auth::user();
    $posts = Post::with([
            'user.currentProfilePhoto',
            'images',
            'loves.user.currentProfilePhoto',
            'views'
        ])
        ->withCount('loves')
        ->inRandomOrder()
        ->take(15)
        ->get();
    $posts->map(function ($post) use ($user) {
        $post->seen_by_user = $post->views->contains('user_id', $user->id);
        $post->created_at_diff = $post->created_at->diffForHumans();
        $post->user_loved = $post->loves->contains('user_id', $user->id);
        $post->view_count = $post->views->count();
        return $post;
    });
    return response()->json($posts);
}
}
<?php
namespace App\Http\Controllers;
use App\Models\Comment;
use Illuminate\Http\Request;
class CommentController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'body' => 'nullable|string',
        'post_id' => 'required|exists:posts,id',
        'parent_id' => 'nullable|exists:comments,id',
        'images.*' => 'nullable|image|max:2048',
    ]);
    $comment = Comment::create([
        'user_id' => auth()->id(),
        'post_id' => $request->post_id,
        'parent_id' => $request->parent_id,
        'body' => $request->body,
    ]);
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('comment_images', 'public');
            $comment->images()->create(['path' => $path]);
        }
    }
    $comment->load('user', 'images', 'replies');
    return response()->json($comment);
}
}
<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class AuthController extends Controller
{
    public function login_post(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);
        $login_type = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        $credentials = [
            $login_type => $request->login,
            'password' => $request->password,
        ];
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }
        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'login' => 'required|string|unique:users,email|unique:users,mobile',
            'password' => 'required|string|min:6',
            'birthday_day' => 'required|integer',
            'birthday_month' => 'required|integer',
            'birthday_year' => 'required|integer',
            'gender' => 'required|in:male,female',
        ]);
        $birthday = sprintf('%04d-%02d-%02d', $request->birthday_year, $request->birthday_month, $request->birthday_day);
        $is_email = filter_var($request->login, FILTER_VALIDATE_EMAIL);
        $user = \App\Models\User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $is_email ? $request->login : null,
            'mobile' => !$is_email ? $request->login : null,
            'password' => Hash::make($request->password),
            'birthday' => $birthday,
            'gender' => $request->gender,
        ]);
        Auth::login($user);

            return redirect()->intended(route('home'));
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
@extends('layouts.app')
@section('styles')
    <link rel="stylesheet" href="{{ mix('css/indexPage.css') }}">
    <link rel="stylesheet" href="{{ mix('css/post.css') }}">
@endsection
@section('title', 'الرئيسية')
@section('content')
@auth
<div class="fb-post-container">
    <form id="postForm" enctype="multipart/form-data">
        @csrf
        <div class="card p-3 mb-4 shadow-sm" style="max-width: 600px; margin: auto;">
            <div class="d-flex mb-3">
            <img src="{{ $user->currentProfilePhoto ? asset('storage/' . $user->currentProfilePhoto->path) : asset('image/default-user-photo.png') }}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
            <textarea class="form-control ms-2" rows="2" placeholder="What's on your mind?" name="body" id="body"></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <label class="btn btn-sm btn-light border">
                        📷
                        <input type="file" name="images[]" id="images" multiple hidden>
                    </label>
                </div>
                <button class="btn btn-primary btn-sm w-100 m-2" type="submit">post now !</button>
            </div>
            <div id="postError" class="text-danger mt-2" style="font-size: 14px;"></div>
        </div>
    </form>
</div>
<div class="fb-post-container" id="post-container">
    @foreach($posts as $post)
        <div class="fb-post">
            <div class="fb-post-header">
                <img src="{{ $post->user->currentProfilePhoto ? asset('storage/' . $post->user->currentProfilePhoto->path) : asset('image/default-user-photo.png') }}"
                     class="profile-img shadow" style="width:32px;height:32px;border-radius:50%;margin-right:8px; object-fit: cover;">
                <strong>{{ $post->user->first_name }} {{ $post->user->last_name }}</strong>
                <span class="timestamp">{{ $post->created_at->diffForHumans() }}</span>
            </div>
            <div class="fb-post-body">
                <p>{{ $post->body }}</p>
                @if($post->images->count())
                    <div class="fb-post-images">
                        @foreach($post->images as $image)
                            <img src="{{ asset('storage/' . $image->path) }}"
                                 onclick="openCarousel({{ $post->id }}, {{ $loop->index }})">
                        @endforeach
                    </div>
                @endif
                <hr>
                <div class="d-flex align-items-center justify-content-between">
                    @php
                    $userLoved = $post->loves->contains('user_id', auth()->id());
                    @endphp
                    <div>
                        <button class="love-btn" onclick="toggleLove({{ $post->id }}, this)">
                            <i id="love-icon-{{ $post->id }}"class="{{ $userLoved ? 'fa-solid' : 'fa-regular' }} fa-heart fa-regular fa-heart "style="{{ $userLoved ? 'color:#ff0000;' : 'color:black;' }}"></i>
                        </button>
                        <span id="love-count-{{ $post->id }}" class="love-count" onclick="showLoveList({{ $post->id }})" style="cursor:pointer;">
                            {{ $post->loves->count() }}
                        </span>
                    </div>
                    <span>👁️ {{ $post->view_count }}</span>
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="modal fade" id="loveListModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">المعجبون بالمنشور</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="loveListBody">
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="postImageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark text-white">
      <div class="modal-body p-0">
        <div id="postCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner" id="postCarouselInner"></div>
          <button class="carousel-control-prev" type="button" data-bs-target="#postCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#postCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
function showLoveList(postId) {
    const post = posts.find(p => p.id === postId);
    if (!post || !post.loves || post.loves.length === 0) {
        document.getElementById('loveListBody').innerHTML = '<p>لا يوجد معجبون حتى الآن.</p>';
    } else {
        const html = post.loves.map(love => `
            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                <img src="${love.user.current_profile_photo ? '/storage/' + love.user.current_profile_photo.path : '{{ asset('image/default-user-photo.png') }}'}" style="width: 32px; height: 32px; border-radius: 50%; margin-right: 10px;">
                <span>${love.user.first_name} ${love.user.last_name}</span>
            </div>
        `).join('');
        document.getElementById('loveListBody').innerHTML = html;
    }
    new bootstrap.Modal(document.getElementById('loveListModal')).show();
}
function toggleLove(postId, btn) {
    btn.classList.add('clicked');
    setTimeout(() => {
        btn.classList.remove('clicked');
    }, 400);
    fetch(`/posts/${postId}/love`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById(`love-count-${postId}`).textContent = data.count;
        const icon = document.getElementById(`love-icon-${postId}`);
        if (data.loved) {
            icon.classList.remove('fa-regular');
            icon.classList.add('fa-solid');
            icon.style.cssText = "color: #ff0000 !important;";
        } else {
            icon.classList.remove('fa-solid');
            icon.classList.add('fa-regular');
            icon.style.color = '';
        }
        const post = posts.find(p => p.id === postId);
        if (post) {
            if (data.loved) {
                post.loves.push({
                    user_id: {{ auth()->id() }},
                    user: {
                        first_name: '{{ auth()->user()->first_name }}',
                        last_name: '{{ auth()->user()->last_name }}',
                        current_profile_photo: {!! auth()->user()->currentProfilePhoto ? json_encode([
                            'path' => auth()->user()->currentProfilePhoto->path
                        ]) : 'null' !!}
                    }
                });
            } else {
                post.loves = post.loves.filter(love => love.user_id !== {{ auth()->id() }});
            }
        }
    });
}
    const posts = @json($posts);
    function openCarousel(postId, startIndex = 0) {
        const post = posts.find(p => p.id === postId);
        if (!post || !post.images.length) return;
        const inner = document.getElementById('postCarouselInner');
        inner.innerHTML = '';
        post.images.forEach((img, idx) => {
            inner.innerHTML += `
                <div class="carousel-item ${idx === startIndex ? 'active' : ''}">
                    <img src="/storage/${img.path}" class="d-block w-100" style="max-height: 80vh; object-fit: contain;">
                </div>
            `;
        });
        new bootstrap.Modal(document.getElementById('postImageModal')).show();
    }
    document.getElementById('postForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const errorDiv = document.getElementById('postError');
    errorDiv.innerHTML = ''; 
    fetch("{{ route('posts.store') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            errorDiv.innerText = data.error;
        } else {
            form.reset(); 
            prependPost(data); 
        }
    })
    .catch(error => {
        console.error(error);
        errorDiv.innerText = 'حدث خطأ أثناء النشر';
    });
});
    let skip = 10;
    let loading = false;
    window.addEventListener('scroll', () => {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
            loadMorePosts();
        }
    });
    function loadMorePosts() {
        if (loading) return;
        loading = true;
        fetch(`/load-posts?skip=${skip}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(post => {
                    renderPost(post);
                    posts.push(post); 
                });
                skip += data.length;
                loading = false;
            });
    }
    function renderPost(post, prepend = false) {
        const container = document.getElementById('post-container');
        const imgSrc = post.user.current_profile_photo
            ? `/storage/${post.user.current_profile_photo.path}`
            : `{{ asset('image/default-user-photo.png') }}`;
const postHtml = `
    <div class="fb-post">
        <div class="fb-post-header">
            <img src="${imgSrc}" class="profile-img" style="width:32px;height:32px;border-radius:50%;margin-right:8px;">
            <strong>${post.user.first_name} ${post.user.last_name}</strong>
            <span class="timestamp">${post.created_at_diff}</span>
        </div>
        <div class="fb-post-body">
            <p>${post.body || ''}</p>
            ${post.images.length ? `
            <div class="fb-post-images">
                ${post.images.map((img, idx) => `
                    <img src="/storage/${img.path}" onclick="openCarousel(${post.id}, ${idx})">
                `).join('')}
            </div>` : ''}
        </div>
        <div class="fb-post-actions mt-2">
            <div class="d-flex align-items-center justify-content-between mt-2">
                <div>
                    <button class="love-btn" onclick="toggleLove(${post.id}, this)">
                        <i id="love-icon-${post.id}"
                        class="${post.user_loved ? 'fa-solid' : 'fa-regular'} fa-heart"
                        style="${post.user_loved ? 'color:#ff0000 !important;' : 'color:black;'}"></i>
                    </button>
                    <span id="love-count-${post.id}" class="love-count" style="cursor:pointer;" onclick="showLoveList(${post.id})">
                        ${post.loves.length}
                    </span>
                </div>
                <span>👁️ ${post.view_count}</span>
            </div>
        </div>
    </div>
`;
        if (prepend) {
            container.insertAdjacentHTML('afterbegin', postHtml);
        } else {
            container.insertAdjacentHTML('beforeend', postHtml);
        }
    }
function prependPost(post) {
    const container = document.getElementById('post-container');
    const profileImage = post.user.current_profile_photo
        ? `/storage/${post.user.current_profile_photo.path}`
        : "/images/default-user-photo.png";
    const imagesHTML = post.images.map((img, idx) => `
        <img src="/storage/${img.path}" onclick="openCarousel(${post.id}, ${idx})">
    `).join('');
    post.view_count = post.view_count ?? 0;
    const postHTML = `
        <div class="fb-post">
            <div class="fb-post-header">
                <img src="${profileImage}" alt="Profile" class="profile-pic shadow" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 5px; object-fit:cover;">
                <strong>${post.user.first_name} ${post.user.last_name}</strong>
                <span class="timestamp">الآن</span>
            </div>
            <div class="fb-post-body">
                <p>${post.body ?? ''}</p>
                ${imagesHTML ? `<div class="fb-post-images">${imagesHTML}</div>` : ''}
                <hr>
                <div class="fb-post-actions mt-2">
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <div>
                            <button class="love-btn" onclick="toggleLove(${post.id}, this)">
                                <i id="love-icon-${post.id}"
                                class="${post.user_loved ? 'fa-solid' : 'fa-regular'} fa-heart"
                                style="${post.user_loved ? 'color:#ff0000 !important;' : 'color:black;'}">
                                </i>
                            </button>
                            <span id="love-count-${post.id}" class="love-count" style="cursor:pointer;" onclick="showLoveList(${post.id})">
                                ${post.loves.length}
                            </span>
                        </div>
                        <span>👁️ ${post.view_count}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('afterbegin', postHTML);
    posts.unshift(post); 
}
</script>
@endauth
@endsection