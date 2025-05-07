@extends('layouts.modele')
@section('title', 'Créer un contrôle')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/controle-create.css') }}">

    <div class="page-background controls-background"></div>

    <div class="controls-dashboard">
        <div class="container">
            <div class="dashboard-header" data-aos="fade-down">
                <div class="course-info">
                    <h1 class="dashboard-title">Créer un contrôle</h1>
                    <p class="course-name">{{ $course->intitule }}</p>
                </div>
            </div>

            @if (session('error'))
                <x-alert type="danger" :message="session('error')" />
            @endif

            <div class="form-container" data-aos="fade-up">
                <form action="{{ route('controles.store', $course->id) }}" method="POST" class="control-form">
                    @csrf

                    <div class="form-group">
                        <label for="titre">Titre du contrôle *</label>
                        <input type="text" id="titre" name="titre" class="form-control @error('titre') is-invalid @enderror"
                            value="{{ old('titre') }}" required>
                        @error('titre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description (optionnel)</label>
                        <textarea id="description" name="description"
                            class="form-control @error('description') is-invalid @enderror"
                            rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group form-col">
                            <label for="date_controle">Date du contrôle *</label>
                            <input type="date" id="date_controle" name="date_controle"
                                class="form-control @error('date_controle') is-invalid @enderror"
                                value="{{ old('date_controle') ?? now()->format('Y-m-d') }}" required>
                            @error('date_controle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group form-col">
                            <label for="coefficient">Coefficient *</label>
                            <input type="number" id="coefficient" name="coefficient"
                                class="form-control @error('coefficient') is-invalid @enderror"
                                value="{{ old('coefficient') ?? 1 }}" min="0.1" max="10" step="0.1" required>
                            @error('coefficient')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('controles.index', $course->id) }}" class="btn-cancel">
                            <i class="bi bi-x-lg"></i>
                            <span>Annuler</span>
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-check-lg"></i>
                            <span>Créer le contrôle</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            AOS.init({
                duration: 800,
                once: true,
                offset: 50
            });
        });
    </script>
@endsection