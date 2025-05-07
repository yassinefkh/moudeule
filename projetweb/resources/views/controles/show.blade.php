@extends('layouts.modele')
@section('title', $controle->titre)

@section('content')

    <link rel="stylesheet" href="{{ asset('/css/controle-show.css') }}">

    <div class="page-background controls-background"></div>

    <div class="controls-dashboard">
        <div class="container">
            <div class="dashboard-header" data-aos="fade-down">
                <div class="course-info">
                    <h1 class="dashboard-title">{{ $controle->titre }}</h1>
                    <p class="course-name">{{ $course->intitule }}</p>
                </div>

                @if(Auth::user()->type === 'enseignant' || Auth::user()->isAdmin())
                    <div class="header-actions">
                        <a href="{{ route('notes.create', [$course->id, $controle->id]) }}" class="btn-primary">
                            <i class="bi bi-pencil-square"></i>
                            <span>Saisir les notes</span>
                        </a>
                        <div class="dropdown">
                            <button class="btn-options" id="dropdownOptions" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownOptions">
                                <li>
                                    <a class="dropdown-item" href="{{ route('controles.edit', [$course->id, $controle->id]) }}">
                                        <i class="bi bi-pencil"></i> Modifier
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('controles.destroy', [$course->id, $controle->id]) }}" method="POST"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce contrôle ? Cette action est irréversible.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif

            @if (session('error'))
                <x-alert type="danger" :message="session('error')" />
            @endif

            <div class="dashboard-content">
                <div class="control-details-card" data-aos="fade-up">
                    <div class="detail-section">
                        <h3 class="section-title">Informations du contrôle</h3>

                        <div class="detail-grid">
                            <div class="detail-item">
                                <div class="detail-label">Date</div>
                                <div class="detail-value">
                                    <i class="bi bi-calendar-event"></i>
                                    {{ \Carbon\Carbon::parse($controle->date_controle)->format('d/m/Y') }}
                                </div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-label">Coefficient</div>
                                <div class="detail-value">
                                    <i class="bi bi-calculator"></i>
                                    {{ $controle->coefficient }}
                                </div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-label">Nombre d'étudiants notés</div>
                                <div class="detail-value">
                                    <i class="bi bi-people"></i>
                                    {{ $notes->count() }} / {{ $students->count() }}
                                </div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-label">Moyenne de la classe</div>
                                <div class="detail-value">
                                    <i class="bi bi-bar-chart"></i>
                                    {{ $controle->moyenne() ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        @if($controle->description)
                            <div class="control-description">
                                <h4 class="description-title">Description</h4>
                                <p>{{ $controle->description }}</p>
                            </div>
                        @endif
                    </div>

                    @if(Auth::user()->type === 'etudiant')
                        <div class="student-grade-section">
                            <h3 class="section-title">Ma note</h3>

                            @php $note = $controle->getNoteForUser(Auth::id()) @endphp

                            @if($note)
                                <div class="grade-display {{ $note->note < 10 ? 'failing' : 'passing' }}">
                                    <div class="grade-value">{{ $note->note }}<span class="grade-max">/20</span></div>

                                    @if($note->commentaire)
                                        <div class="grade-comment">
                                            <i class="bi bi-chat-quote"></i>
                                            <span>{{ $note->commentaire }}</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="no-grade">
                                    <i class="bi bi-hourglass-split"></i>
                                    <span>Pas encore noté</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(Auth::user()->type === 'enseignant' || Auth::user()->isAdmin())
                        <div class="stats-section">
                            <h3 class="section-title">Statistiques</h3>

                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-label">Note la plus haute</div>
                                    <div class="stat-value">{{ $notes->count() > 0 ? $notes->max('note') : 'N/A' }}</div>
                                </div>

                                <div class="stat-item">
                                    <div class="stat-label">Note la plus basse</div>
                                    <div class="stat-value">{{ $notes->count() > 0 ? $notes->min('note') : 'N/A' }}</div>
                                </div>

                                <div class="stat-item">
                                    <div class="stat-label">Étudiants ≥ 10</div>
                                    <div class="stat-value">{{ $notes->where('note', '>=', 10)->count() }}</div>
                                </div>

                                <div class="stat-item">
                                    <div class="stat-label">Étudiants < 10</div>
                                            <div class="stat-value">{{ $notes->where('note', '<', 10)->count() }}</div>
                                    </div>
                                </div>
                            </div>
                    @endif
                    </div>

                    @if(Auth::user()->type === 'enseignant' || Auth::user()->isAdmin())
                        <div class="grades-list-container" data-aos="fade-up">
                            <h3 class="section-title">Notes des étudiants</h3>

                            @if($students->isEmpty())
                                <div class="empty-message">
                                    <i class="bi bi-people"></i>
                                    <span>Aucun étudiant inscrit à ce cours</span>
                                </div>
                            @else
                                <div class="grades-table-wrapper">
                                    <table class="grades-table">
                                        <thead>
                                            <tr>
                                                <th>Étudiant</th>
                                                <th>Note</th>
                                                <th>Commentaire</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($students as $student)
                                                @php $studentNote = $notes->where('user_id', $student->id)->first(); @endphp
                                                <tr>
                                                    <td class="student-cell">
                                                        <div class="student-avatar">
                                                            {{ strtoupper(substr($student->prenom, 0, 1) . substr($student->nom, 0, 1)) }}
                                                        </div>
                                                        <div class="student-info">
                                                            <div class="student-name">{{ $student->prenom }} {{ $student->nom }}</div>
                                                        </div>
                                                    </td>
                                                    <td
                                                        class="grade-cell {{ $studentNote && $studentNote->note < 10 ? 'failing' : '' }}">
                                                        {{ $studentNote ? $studentNote->note . '/20' : 'Non noté' }}
                                                    </td>
                                                    <td class="comment-cell">
                                                        {{ $studentNote && $studentNote->commentaire ? $studentNote->commentaire : '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="actions-footer" data-aos="fade-up">
                        <a href="{{ route('controles.index', $course->id) }}" class="btn-back">
                            <i class="bi bi-arrow-left"></i>
                            <span>Retour aux contrôles</span>
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