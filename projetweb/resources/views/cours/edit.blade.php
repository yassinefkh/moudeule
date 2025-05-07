@extends('layouts.modele')
@section('title', 'Modifier un cours')

@section('content')

    <link rel="stylesheet" href="{{ asset('/css/cours-edit.css') }}">

    <div class="edit-course-container">
        <div class="form-card">
            <div class="form-title">
                <i class="bi bi-pencil-square me-2"></i>Modifier le cours "{{ $course->intitule }}"
            </div>

            <form method="POST" action="{{ route('cours.update', $course->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="intitule" class="form-label">Intitulé du cours</label>
                    <input type="text" id="intitule" name="intitule"
                        class="form-control @error('intitule') is-invalid @enderror"
                        value="{{ old('intitule', $course->intitule) }}" required>

                    @error('intitule')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="enseignant" class="form-label">Enseignant</label>
                    <select name="user_id" id="enseignant" class="form-select @error('user_id') is-invalid @enderror"
                        required>
                        @foreach ($enseignants as $enseignant)
                            <option value="{{ $enseignant->id }}" {{ $enseignant->id === $course->user->id ? 'selected' : '' }}>
                                {{ $enseignant->prenom }} {{ $enseignant->nom }}
                            </option>
                        @endforeach
                    </select>

                    @error('user_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="btn-actions">
                    <a href="{{ route('cours.index') }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection