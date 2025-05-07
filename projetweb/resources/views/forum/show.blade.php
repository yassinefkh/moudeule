@extends('layouts.modele')
@section('title', $post->titre)
@section('content')

    <link rel="stylesheet" href="{{ asset('/css/forum-show.css') }}">

    <div class="discussion-container">
        <div class="thread-card">
            <div class="thread-title">{{ $post->titre }}</div>
            <div class="thread-meta">Posté par {{ $post->user->prenom }} {{ $post->user->nom }} le
                {{ $post->created_at->format('d/m/Y H:i') }}</div>
            <div class="thread-content">{{ $post->contenu }}</div>
        </div>

        <div class="comment-thread">
            <h4 class="mb-4">Commentaires</h4>
            @include('forum.partials.comments', ['comments' => $post->comments->whereNull('parent_id'), 'depth' => 0])
        </div>

        <form action="{{ route('comments.store') }}" method="POST" class="add-comment-box">
            @csrf
            <input type="hidden" name="post_id" value="{{ $post->id }}">
            <div class="mb-3">
                <label for="contenu" class="form-label">Ajouter un commentaire</label>
                <input type="text" name="contenu" class="form-control" placeholder="Votre commentaire..." required>
            </div>
            <button type="submit" class="btn btn-submit-comment">
                <i class="bi bi-send-fill me-1"></i> Envoyer
            </button>
            <a href="{{ route('posts.index') }}" class="btn btn-back-comment">
                <i class="bi bi-arrow-left me-1"></i> Retour au forum
            </a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.DarkReader && DarkReader.isEnabled()) {
                document.body.classList.add('dark-mode-active');
            }
        });
    </script>
@endsection