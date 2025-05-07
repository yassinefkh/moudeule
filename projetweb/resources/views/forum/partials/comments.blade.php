<link rel="stylesheet" href="{{ asset('/css/forum-partials-comments.css') }}">

@foreach ($comments as $comment)
    <div class="comment-block depth-{{ $depth }}">
        <div class="comment-header">
            {{ $comment->user->prenom }} {{ $comment->user->nom }}
        </div>
        <div class="comment-date">
            {{ $comment->created_at->format('d/m/Y H:i') }}
        </div>
        <div class="comment-body">
            {{ $comment->contenu }}
        </div>

        <div class="comment-actions">
            <button type="button" onclick="toggleReplyForm({{ $comment->id }})">Répondre</button>
        </div>

        <form action="{{ route('comments.store') }}" method="POST" class="reply-form d-none"
            id="reply-form-{{ $comment->id }}">
            @csrf
            <input type="hidden" name="post_id" value="{{ $comment->post_id }}">
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            <input type="text" name="contenu" class="form-control mt-2" placeholder="Votre réponse..." required>
            <button type="submit" class="btn btn-sm btn-outline-primary mt-2">Envoyer</button>
        </form>

        @if ($comment->replies->count())
            @include('forum.partials.comments', ['comments' => $comment->replies, 'depth' => $depth + 1])
        @endif
    </div>
@endforeach

<script>
    function toggleReplyForm(id) {
        const form = document.getElementById(`reply-form-${id}`);
        if (form) form.classList.toggle('d-none');
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.DarkReader && DarkReader.isEnabled()) {
            document.body.classList.add('dark-mode-active');
        }
    });
</script>