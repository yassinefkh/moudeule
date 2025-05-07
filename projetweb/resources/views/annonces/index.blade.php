@extends('layouts.modele')

@section('title', 'Annonces du cours')

@section('content')

    <link rel="stylesheet" href="{{ asset('/css/annonces-index.css') }}">

    <section class="announcements-page">
        <div class="announcements-container">
            <div class="page-header">
                <h1 class="page-title">Gestion des annonces</h1>
                <div class="course-info">
                    <i class="fas fa-book"></i>
                    {{ $cours->intitule }}
                </div>
            </div>


            <div class="form-section">
                <h2 class="form-title">
                    <i class="fas fa-bullhorn"></i>
                    Publier une nouvelle annonce
                </h2>

                <form action="{{ route('annonces.store', $cours->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="titre" class="form-label">Titre de l'annonce</label>
                        <input type="text" name="titre" id="titre" class="form-control"
                            placeholder="Entrez un titre descriptif" required>
                    </div>

                    <div class="form-group">
                        <label for="contenu" class="form-label">Contenu de l'annonce</label>
                        <textarea name="contenu" id="contenu" class="form-control" rows="5"
                            placeholder="Détaillez votre annonce ici..." required></textarea>
                    </div>

                    <button type="submit" class="form-button">
                        <i class="fas fa-paper-plane"></i>
                        Publier l'annonce
                    </button>
                </form>
            </div>


            <h2 class="form-title">
                <i class="fas fa-list"></i>
                Annonces publiées
            </h2>

            @if($annonces->count() > 0)
                <div class="announcement-list">
                    @foreach($annonces as $annonce)
                        <div class="announcement-item">
                            <h3 class="announcement-title">
                                <i class="fas fa-star"></i>
                                {{ $annonce->titre }}
                            </h3>

                            <div class="announcement-content">
                                {{ $annonce->contenu }}
                            </div>

                            <div class="announcement-meta">
                                <div class="announcement-date">
                                    <i class="far fa-clock"></i>
                                    {{ $annonce->created_at->diffForHumans() }}
                                </div>

                                <form action="{{ route('annonces.destroy', [$cours->id, $annonce->id]) }}" method="POST"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <i class="fas fa-trash"></i>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <p class="empty-text">Aucune annonce n'a encore été publiée pour ce cours.</p>
                </div>
            @endif
        </div>
    </section>
@endsection