@extends('layouts.modele')
@section('title', 'Contrôles - ' . $course->intitule)

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/controle-index.css') }}">

    <div class="page-background controls-background"></div>

    <div class="controls-dashboard">
        <div class="container">
            <div class="dashboard-header" data-aos="fade-down">
                <div class="course-info">
                    <h1 class="dashboard-title">Contrôles et évaluations</h1>
                    <p class="course-name">{{ $course->intitule }}</p>
                </div>

                @if(Auth::user()->type === 'enseignant' || Auth::user()->isAdmin())
                    <a href="{{ route('controles.create', $course->id) }}" class="btn-create">
                        <div class="btn-icon"><i class="bi bi-plus-lg"></i></div>
                        <span>Nouveau contrôle</span>
                    </a>
                @endif
            </div>

            @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif

            @if (session('error'))
                <x-alert type="danger" :message="session('error')" />
            @endif

            <div class="dashboard-content">
                @if($controles->isEmpty())
                    <div class="empty-state" data-aos="fade-up">
                        <div class="empty-illustration">
                            <i class="bi bi-journals"></i>
                        </div>
                        <h3 class="empty-title">Aucun contrôle n'a été créé</h3>
                        <p class="empty-description">
                            @if(Auth::user()->type === 'enseignant' || Auth::user()->isAdmin())
                                Vous pouvez créer votre premier contrôle pour commencer à évaluer les étudiants.
                            @else
                                Aucun contrôle n'est disponible pour ce cours pour le moment.
                            @endif
                        </p>

                        @if(Auth::user()->type === 'enseignant' || Auth::user()->isAdmin())
                            <a href="{{ route('controles.create', $course->id) }}" class="btn-create-first">
                                <i class="bi bi-plus-circle"></i>
                                <span>Créer un contrôle</span>
                            </a>
                        @endif
                    </div>
                @else
                    <div class="controls-grid" data-aos="fade-up">
                        @foreach($controles as $controle)
                            <div class="control-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                                <div class="control-header">
                                    <div class="control-icon">
                                        <i class="bi bi-journal-check"></i>
                                    </div>
                                    <div class="control-date">
                                        <i class="bi bi-calendar-event"></i>
                                        {{ \Carbon\Carbon::parse($controle->date_controle)->format('d/m/Y') }}
                                    </div>
                                </div>

                                <h3 class="control-title">{{ $controle->titre }}</h3>

                                @if($controle->description)
                                    <div class="control-description">
                                        {{ \Illuminate\Support\Str::limit($controle->description, 100) }}
                                    </div>
                                @endif

                                <div class="control-details">
                                    <div class="control-info">
                                        <div class="info-item">
                                            <span class="label">Coefficient</span>
                                            <span class="value">{{ $controle->coefficient }}</span>
                                        </div>

                                        @if(Auth::user()->type === 'enseignant' || Auth::user()->isAdmin())
                                            <div class="info-item">
                                                <span class="label">Notes saisies</span>
                                                <span class="value">{{ $controle->notes->count() }} /
                                                    {{ $course->students->count() }}</span>
                                            </div>
                                        @endif

                                        <div class="info-item">
                                            <span class="label">Moyenne</span>
                                            <span class="value">{{ $controle->moyenne() ?? 'N/A' }}</span>
                                        </div>

                                        @if(Auth::user()->type === 'etudiant')
                                            <div class="info-item">
                                                <span class="label">Ma note</span>
                                                @php $note = $controle->getNoteForUser(Auth::id()) @endphp
                                                <span class="value {{ $note && $note->note < 10 ? 'text-danger' : '' }}">
                                                    {{ $note ? $note->note . '/20' : 'Non noté' }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="control-actions">
                                        <a href="{{ route('controles.show', [$course->id, $controle->id]) }}" class="btn-view">
                                            <span>Détails</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>

                                        @if(Auth::user()->type === 'enseignant' || Auth::user()->isAdmin())
                                            <a href="{{ route('notes.create', [$course->id, $controle->id]) }}" class="btn-grades">
                                                <i class="bi bi-pencil-square"></i>
                                                <span>Saisir notes</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="actions-footer" data-aos="fade-up">
                    <a href="{{ route('cours.show', $course->id) }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i>
                        <span>Retour au cours</span>
                    </a>
                </div>
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