@extends('layouts.modele')
@section('title', 'Détails de la formation')

@section('content')

    <link rel="stylesheet" href="{{ asset('/css/formations-show.css') }}">

    <div class="formation-container">
        <div class="formation-header">
            <h1><i class="bi bi-mortarboard-fill me-2"></i> {{ $formation->intitule }}</h1>
            <p>Toutes les informations sur votre formation, ses étudiants, enseignants et matières enseignées.</p>
        </div>

        <div class="status-indicators">
            <div class="status-card">
                <div class="icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="count">{{ $etudiants->count() }}</div>
                <div class="label">Étudiants inscrits</div>
            </div>

            <div class="status-card">
                <div class="icon">
                    <i class="bi bi-person-workspace"></i>
                </div>
                <div class="count">{{ $enseignants->count() }}</div>
                <div class="label">Enseignants assignés</div>
            </div>

            <div class="status-card">
                <div class="icon">
                    <i class="bi bi-journal-bookmark"></i>
                </div>
                <div class="count">{{ $cours->count() }}</div>
                <div class="label">Matières enseignées</div>
            </div>
        </div>

        <div class="details-card">
            <h3><i class="bi bi-people-fill"></i> Étudiants inscrits</h3>
            @if ($etudiants->isEmpty())
                <div class="empty-message">
                    <i class="bi bi-emoji-neutral"></i>
                    Aucun étudiant inscrit pour le moment.
                </div>
            @else
                <ul class="person-list">
                    @foreach ($etudiants as $etudiant)
                        <li class="person-item">
                            <div class="person-avatar">
                                {{ substr($etudiant->prenom, 0, 1) }}{{ substr($etudiant->nom, 0, 1) }}
                            </div>
                            <div class="person-info">
                                <div class="person-name">{{ $etudiant->prenom }} {{ $etudiant->nom }}</div>
                                <div class="person-details">Étudiant</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="content-section">
            <div class="details-card">
                <h3><i class="bi bi-person-workspace"></i> Enseignants assignés</h3>
                @if ($enseignants->isEmpty())
                    <div class="empty-message">
                        <i class="bi bi-emoji-neutral"></i>
                        Aucun enseignant assigné.
                    </div>
                @else
                    <ul class="person-list">
                        @foreach ($enseignants as $enseignant)
                            <li class="person-item">
                                <div class="person-avatar">
                                    {{ substr($enseignant->prenom, 0, 1) }}{{ substr($enseignant->nom, 0, 1) }}
                                </div>
                                <div class="person-info">
                                    <div class="person-name">{{ $enseignant->prenom }} {{ $enseignant->nom }}</div>
                                    <div class="person-details">Enseignant</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="details-card">
                <h3><i class="bi bi-journal-bookmark"></i> Matières enseignées</h3>
                @if ($cours->isEmpty())
                    <div class="empty-message">
                        <i class="bi bi-emoji-neutral"></i>
                        Aucune matière pour cette formation.
                    </div>
                @else
                    <ul class="course-list">
                        @foreach ($cours as $coursItem)
                            <li class="course-item">
                                <div class="course-title">{{ $coursItem->intitule }}</div>
                                <div class="course-instructor">
                                    <i class="bi bi-person"></i> {{ $coursItem->user->prenom }} {{ $coursItem->user->nom }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection