<div id="comments-{{ $post->id }}">
    @foreach($post->comments->where('parent_id', null) as $comment)
        @include('components.comment', ['comment' => $comment])
    @endforeach

    {{-- نموذج تعليق --}}
    <form class="comment-form mt-3" data-post-id="{{ $post->id }}">
        @csrf
        <input type="hidden" name="post_id" value="{{ $post->id }}">
        <input type="hidden" name="parent_id" class="parent-id">
        <textarea name="body" class="form-control comment-body" rows="2" placeholder="اكتب تعليقًا..."></textarea>
        <button class="btn btn-sm btn-primary mt-2">نشر</button>
    </form>
</div>
