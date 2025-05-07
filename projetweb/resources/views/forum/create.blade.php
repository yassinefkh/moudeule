@extends('layouts.modele')
@section('title', 'Créer un post')
@section('content')

    <link rel="stylesheet" href="{{ asset('/css/forum-create.css') }}">

    <div class="form-container mt-5">
        <h2>Créer un nouveau post</h2>
        <form action="{{ route('posts.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" name="titre" class="form-control" value="{{ old('titre') }}" required>
            </div>
            <div class="mb-3">
                <label for="contenu" class="form-label">Contenu</label>
                <textarea name="contenu" class="form-control" rows="6" required>{{ old('contenu') }}</textarea>
            </div>
            <div class="d-flex justify-content-between">
                <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary btn-publish">Annuler</a>
                <button type="submit" class="btn btn-success btn-publish">
                    <i class="bi bi-upload me-1"></i> Publier
                </button>
            </div>
        </form>
    </div>
@endsection