@extends('layouts.modele')
@section('title', 'Créer une formation')
@section('content')

    <link rel="stylesheet" href="{{ asset('/css/admin-formations-create.css') }}">

    <div class="form-container">
        <div class="header-section">
            <h2><i class="bi bi-plus-circle me-2"></i>Créer une formation</h2>
        </div>

        <div class="form-card">
            <form method="POST" action="{{ route('admin.formations.store') }}">
                @csrf

                <div class="form-group">
                    <label for="intitule" class="form-label">Intitulé de la formation</label>
                    <input type="text" name="intitule" id="intitule"
                        class="form-control @error('intitule') is-invalid @enderror" value="{{ old('intitule') }}"
                        placeholder="Saisissez l'intitulé de la formation" required autofocus>
                    @error('intitule')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="btn-container">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Enregistrer
                    </button>
                    <a href="{{ route('admin.formations.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection