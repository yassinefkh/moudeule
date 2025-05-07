@extends('layouts.modele')
@section('title', 'Créer un utilisateur')
@section('content')

    <link rel="stylesheet" href="{{ asset('/css/admin-create-user.css') }}">


    <div class="create-user-container">
        <h2 class="create-user-title">Créer un nouvel utilisateur</h2>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="form-group mb-3">
                <label for="login">Login</label>
                <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus
                    class="form-control @error('login') is-invalid @enderror">
                @error('login')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="nom">Nom</label>
                <input id="nom" type="text" name="nom" value="{{ old('nom') }}" required
                    class="form-control @error('nom') is-invalid @enderror">
                @error('nom')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="prenom">Prénom</label>
                <input id="prenom" type="text" name="prenom" value="{{ old('prenom') }}" required
                    class="form-control @error('prenom') is-invalid @enderror">
                @error('prenom')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="mdp">Mot de passe</label>
                <input id="mdp" type="password" name="mdp" required class="form-control @error('mdp') is-invalid @enderror">
                @error('mdp')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="mdp-confirm">Confirmer le mot de passe</label>
                <input id="mdp-confirm" type="password" name="mdp_confirmation" required class="form-control">
            </div>

            <div class="form-group mb-3">
                <label for="type">Type</label>
                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                    <option value="etudiant" {{ old('type') == 'etudiant' ? 'selected' : '' }}>Étudiant</option>
                    <option value="enseignant" {{ old('type') == 'enseignant' ? 'selected' : '' }}>Enseignant</option>
                    <option value="admin" {{ old('type') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                </select>
                @error('type')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            @if (old('type') !== 'admin' && old('type') !== 'enseignant')
                <div class="form-group mb-3">
                    <label for="formation_id">Formation</label>
                    <select name="formation_id" id="formation_id"
                        class="form-select @error('formation_id') is-invalid @enderror">
                        <option value="">Sélectionnez une formation</option>
                        @foreach ($formations as $formation)
                            <option value="{{ $formation->id }}" {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                                {{ $formation->intitule }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text">Optionnel pour les enseignants et administrateurs</small>
                    @error('formation_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <div class="form-group mt-4 d-flex justify-content-center">
                <button type="submit" class="btn btn-submit">Enregistrer</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-cancel">Annuler</a>
            </div>
        </form>
    </div>

@endsection