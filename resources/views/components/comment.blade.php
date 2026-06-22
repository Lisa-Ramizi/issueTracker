<div class="comment">
    <div class="comment__author">{{ $comment->author_name }}</div>
    <div class="comment__time">{{ $comment->created_at->diffForHumans() }}</div>
    <p class="comment__body">{{ $comment->body }}</p>
</div>
