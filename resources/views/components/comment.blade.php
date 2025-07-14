
@php
    $maxLength = 300;
    $isLong = strlen($comment->body) > $maxLength;
    $shortBody = mb_substr($comment->body, 0, $maxLength);
@endphp

<div class="comment mb-3" id="comment-{{ $comment->id }}">
    {{-- التعليق الرئيسي --}}
    <div class="d-flex align-items-center mb-1">
        <img src="{{ $comment->user->currentProfilePhoto
            ? asset('storage/' . $comment->user->currentProfilePhoto->path)
            : asset('images/default-user-photo.png') }}"
            alt="صورة المستخدم"
            class="rounded-circle me-2"
            style="width: 32px; height: 32px; object-fit: cover;">
        <strong>{{ $comment->user->first_name }} {{ $comment->user->last_name }}</strong>
    </div>

    {{-- من رد على من --}}
    @if($comment->parent && $comment->parent->user)
        <p class="text-muted small mb-1 ps-4">
            ردًا على <strong>{{ $comment->parent->user->first_name }} {{ $comment->parent->user->last_name }}</strong>
        </p>
    @endif

    {{-- محتوى التعليق --}}
    <div class="ps-4">
@php
    $maxLength = 300;
    $isLong = strlen($comment->body) > $maxLength;
    $shortBody = nl2br(e(mb_substr($comment->body, 0, $maxLength))) . '...';
    $fullBody = nl2br(e($comment->body));
@endphp

<p class="mb-1 comment-body" id="comment-body-{{ $comment->id }}">
    {!! $isLong ? $shortBody : $fullBody !!}
</p>

@if($isLong)
    <button class="btn btn-link p-0"
            onclick="toggleFullComment({{ $comment->id }})"
            id="toggle-comment-{{ $comment->id }}"
            data-full="{{ $fullBody }}"
            data-short="{{ $shortBody }}">
        عرض المزيد
    </button>
@endif
        {{-- صور التعليق --}}
        @if($comment->images && $comment->images->count())
            <div class="d-flex flex-wrap gap-2 mb-2">
                @foreach($comment->images as $image)
                    <img src="{{ asset('storage/' . $image->path) }}"
                         class="img-thumbnail"
                         style="width: 100px; height: 100px; object-fit: cover;">
                @endforeach
            </div>
        @endif

        {{-- أدوات التعليق --}}
        <div class="d-flex align-items-center gap-3 mb-2">
            <span style="cursor:pointer;" onclick="toggleCommentLove({{ $comment->id }})">
                ❤️ <span id="comment-love-count-{{ $comment->id }}">{{ $comment->loves->count() }}</span>
            </span>
            <span style="cursor:pointer;" onclick="showCommentLoveList({{ $comment->id }})">
                👥 عرض المعجبين
            </span>
            <span style="cursor:pointer;" onclick="replyTo('{{ $comment->user->first_name }}', {{ $comment->id }}, {{ $comment->post_id }})">
                💬 رد
            </span>
        </div>
    </div>

    {{-- الردود المتداخلة --}}
    @if($comment->replies && $comment->replies->count())
        <div class="ps-4 mt-2">
            @foreach($comment->replies as $reply)
                @include('components.comment', ['comment' => $reply])
            @endforeach
        </div>
    @endif
</div>

