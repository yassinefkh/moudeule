@extends('layouts.modele')
@section('title', 'Modifier une formation')
@section('content')

    <link rel="stylesheet" href="{{ asset('/css/admin-formations-edit.css') }}">

    <div class="form-container">
        <div class="header-section">
            <h2><i class="bi bi-pencil-square me-2"></i>Modifier la formation</h2>
        </div>

        <div class="form-card">
            <div class="form-metadata">
                <div>
                    <span class="fw-bold">ID:</span> {{ $formation->id }}
                </div>
                <div>
                    <span class="fw-bold">Dernière modification:</span>
                    {{ \Carbon\Carbon::parse($formation->updated_at)->format('d/m/Y à H:i') }}
                </div>
            </div>

            <form method="POST" action="{{ route('admin.formations.update', $formation->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="intitule" class="form-label">Intitulé de la formation</label>
                    <input type="text" name="intitule" id="intitule"
                        class="form-control @error('intitule') is-invalid @enderror"
                        value="{{ old('intitule', $formation->intitule) }}" required autofocus>
                    @error('intitule')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="btn-container">
                    <a href="{{ route('admin.formations.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection