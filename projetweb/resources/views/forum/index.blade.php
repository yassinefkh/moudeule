@extends('layouts.modele')
@section('title', 'Forum | Discussions')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/forum-index.css') }}">

    <div class="page-background forum-background"></div>

    <div class="forum-container">
        <div class="forum-header-wrapper" data-aos="fade-down">
            <div class="forum-header">
                <div class="forum-header-content">
                    <h1 class="forum-title">
                        <i class="bi bi-chat-square-text-fill"></i>
                        <span>Forum de discussion</span>
                    </h1>
                    <p class="forum-description">Échangez des idées, posez vos questions et partagez vos connaissances</p>
                </div>

                <div class="forum-actions">
                    <a href="{{ route('posts.create') }}" class="btn-create-post">
                        <span class="btn-icon"><i class="bi bi-plus-lg"></i></span>
                        <span class="btn-text">Nouveau sujet</span>
                    </a>
                </div>
            </div>

            <div class="forum-stats">
                <div class="stat-item">
                    <div class="stat-icon"><i class="bi bi-chat-dots"></i></div>
                    <div class="stat-data">
                        <span class="stat-value">{{ App\Models\Post::count() }}</span>
                        <span class="stat-label">Discussions</span>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon"><i class="bi bi-reply"></i></div>
                    <div class="stat-data">
                        <span class="stat-value">{{ App\Models\Post::sum('comments_count') }}</span>
                        <span class="stat-label">Réponses</span>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        @if (session('error'))
            <x-alert type="danger" :message="session('error')" />
        @endif

        <div class="posts-container">
            @forelse ($posts as $post)
                    <div class="post-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <div class="post-header">
                            <div class="post-avatar">
                                {{ strtoupper(substr($post->user->prenom, 0, 1) . substr($post->user->nom, 0, 1)) }}
                            </div>
                            <div class="post-meta">
                                <div class="post-author">{{ $post->user->prenom }} {{ $post->user->nom }}</div>
                                <div class="post-timestamp">
                                    <i class="bi bi-clock"></i> {{ $post->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>

                        <div class="post-content">
                            <a href="{{ route('posts.show', $post->id) }}" class="post-title-link">
                                <h2 class="post-title">{{ $post->titre }}</h2>
                            </a>
                            <p class="post-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($post->contenu), 120) }}</p>
                        </div>

                        <div class="post-footer">
                            <div class="post-stats">
                                <div class="stat">
                                    <i class="bi bi-chat"></i>
                                    <span>{{ $post->comments_count ?? $post->comments()->count() }} commentaires</span>
                                </div>
                                <div class="stat">
                                    <i class="bi bi-emoji-smile"></i>
                                    <span>{{ $post->reactions_count }} réactions</span>
                                </div>
                            </div>

                            <div class="post-actions">
                                <a href="{{ route('posts.show', $post->id) }}" class="btn-view-post">
                                    Voir la discussion
                                    <i class="bi bi-arrow-right"></i>
                                </a>

                                @auth
                                    @if (auth()->user()->type === 'admin')
                                        <div class="admin-actions-dropdown">
                                            <button class="btn-admin-menu" title="Options d'administration">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <div class="admin-dropdown-menu">
                                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST"
                                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce post ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="admin-action-btn btn-delete">
                                                        <i class="bi bi-trash"></i>
                                                        <span>Supprimer</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        </div>

                        <div class="post-reactions">
                            @php
                                $reactions = $post->reactions->groupBy('emoji');
                                $userReaction = auth()->check() ? $post->reactions->where('user_id', auth()->id())->first() : null;
                            @endphp

                            <div class="reactions-display">
                                @foreach($reactions as $emoji => $reactionGroup)
                                    <div
                                        class="reaction-badge {{ $userReaction && $userReaction->emoji === $emoji ? 'user-reacted' : '' }}">
                                        <span class="reaction-emoji">{{ $emoji }}</span>
                                        <span class="reaction-count">{{ count($reactionGroup) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            @auth
                                <div class="reaction-button-container">
                                    <button class="btn-add-reaction" title="Ajouter une réaction">
                                        <i class="bi bi-emoji-smile"></i>
                                        <span>Réagir</span>
                                    </button>

                                    <div class="reaction-picker">
                                        <form action="{{ route('reactions.react') }}" method="POST" class="reaction-form">
                                            @csrf
                                            <input type="hidden" name="post_id" value="{{ $post->id }}">

                                            <div class="reactions-grid">
                                                @foreach(['👍', '❤️', '😂', '😮', '😢', '👏', '🎉'] as $emoji)
                                                    <button type="submit" name="emoji" value="{{ $emoji }}"
                                                        class="emoji-button {{ $userReaction && $userReaction->emoji === $emoji ? 'selected' : '' }}">
                                                        {{ $emoji }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
            @empty
                <div class="empty-forum" data-aos="fade-up">
                    <div class="empty-icon">
                        <i class="bi bi-chat-square-text"></i>
                    </div>
                    <h3 class="empty-title">Aucune discussion pour le moment</h3>
                    <p class="empty-description">Soyez le premier à lancer une discussion sur le forum !</p>
                    <a href="{{ route('posts.create') }}" class="btn-create-first-post">
                        <i class="bi bi-plus-circle"></i>
                        <span>Créer une discussion</span>
                    </a>
                </div>
            @endforelse
        </div>

        <div class="pagination-container" data-aos="fade-up">
            {{ $posts->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    once: true
                });
            }

            const adminMenus = document.querySelectorAll('.btn-admin-menu');
            adminMenus.forEach(menu => {
                menu.addEventListener('click', function () {
                    const dropdown = this.nextElementSibling;
                    dropdown.classList.toggle('show');

                    adminMenus.forEach(otherMenu => {
                        if (otherMenu !== menu) {
                            otherMenu.nextElementSibling.classList.remove('show');
                        }
                    });
                });
            });

            const reactionButtons = document.querySelectorAll('.btn-add-reaction');
            reactionButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const picker = this.parentNode.querySelector('.reaction-picker');
                    picker.classList.toggle('show');

                    reactionButtons.forEach(otherButton => {
                        if (otherButton !== button) {
                            otherButton.parentNode.querySelector('.reaction-picker').classList.remove('show');
                        }
                    });
                });
            });

            document.addEventListener('click', function (e) {
                if (!e.target.closest('.admin-actions-dropdown') && !e.target.closest('.reaction-button-container')) {
                    document.querySelectorAll('.admin-dropdown-menu, .reaction-picker').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });
        });
    </script>
@endsection