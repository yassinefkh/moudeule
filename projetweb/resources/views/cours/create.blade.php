@extends('layouts.modele')
@section('title', 'Créer un cours')

@section('content')

    <link rel="stylesheet" href="{{ asset('/css/cours-index.css') }}">

    <div class="container">
        <div class="page-header">
            <h2>Créer un nouveau cours</h2>
        </div>

        <div class="course-form-container">
            <form method="POST" action="{{ route('cours.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="intitule" class="form-label">Intitulé du cours</label>
                    <input type="text" name="intitule" id="intitule"
                        class="form-control @error('intitule') is-invalid @enderror" value="{{ old('intitule') }}" required>
                    @error('intitule')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="formation_id" class="form-label">Formation</label>
                    <select name="formation_id" id="formation_id"
                        class="form-control @error('formation_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Choisir une formation</option>
                        @foreach ($formations as $formation)
                            <option value="{{ $formation->id }}" {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                                {{ $formation->intitule }}
                            </option>
                        @endforeach
                    </select>
                    @error('formation_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="user_id" class="form-label">Enseignant</label>
                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror"
                        required>
                        <option value="" disabled selected>Choisir un enseignant</option>
                        @foreach ($enseignants as $enseignant)
                            <option value="{{ $enseignant->id }}" {{ old('user_id') == $enseignant->id ? 'selected' : '' }}>
                                {{ $enseignant->nom }} {{ $enseignant->prenom }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-plus-circle me-1"></i> Créer le cours
                    </button>
                    <a href="{{ route('cours.index') }}" class="btn btn-back">
                        <i class="bi bi-arrow-left me-1"></i> Retour
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection