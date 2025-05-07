@extends('layouts.modele')
@section('title', 'Mon Planning')
@section('content')
    <link rel="stylesheet" href="{{ asset('/css/planning-etudiant_table.css') }}">

    <div class="planning-page">
        <div class="planning-container">
            <div class="page-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1 class="page-title">Mon Planning</h1>
                        <p class="page-subtitle">Consultez vos séances de cours programmées</p>
                    </div>
                    <div class="header-actions">
                        <div class="user-profile">
                            <div class="user-avatar">
                                {{ substr(Auth::user()->prenom, 0, 1) }}{{ substr(Auth::user()->nom, 0, 1) }}
                            </div>
                            <span class="user-name">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-content">
                <div class="view-options">
                    <h2 class="section-title">Mode d'affichage</h2>
                    <div class="toggle-view">
                        <a href="{{ route('planning.student_sessions') }}" class="view-button calendar-view">
                            <i class="bi bi-calendar3"></i>
                            <span>Calendrier</span>
                        </a>
                        <a href="{{ route('planning.student_sessionsTable') }}" class="view-button table-view active">
                            <i class="bi bi-table"></i>
                            <span>Liste</span>
                        </a>
                    </div>
                </div>

                <div class="filter-controls">
                    <div class="filters-header">
                        <h2 class="section-title">Filtres</h2>
                        <div class="search-container">

                            <input type="text" id="scheduleSearch" placeholder="Rechercher un cours..."
                                class="search-input">
                        </div>
                    </div>

                    <div class="filter-pills">
                        <a href="{{ route('planning.student_sessionsTable') }}"
                            class="filter-pill {{ !request()->has('sort_by_course') && !request()->has('sort') ? 'active' : '' }}">
                            <i class="bi bi-list"></i>
                            <span>Toutes les séances</span>
                        </a>
                        <a href="{{ route('planning.student_sessionsTable', ['sort_by_course' => 1]) }}"
                            class="filter-pill {{ request()->has('sort_by_course') ? 'active' : '' }}">
                            <i class="bi bi-sort-alpha-down"></i>
                            <span>Par cours</span>
                        </a>
                        <a href="{{ route('planning.student_sessionsTable', ['sort' => 'week', 'week' => 'current']) }}"
                            class="filter-pill {{ request()->has('sort') && request('sort') == 'week' ? 'active' : '' }}">
                            <i class="bi bi-calendar-week"></i>
                            <span>Cette semaine</span>
                        </a>
                    </div>
                </div>

                @if ($planning->isEmpty())
                    <div class="empty-state">
                        <div class="empty-illustration">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <h3 class="empty-title">Aucune séance programmée</h3>
                        <p class="empty-message">Il n'y a aucune séance de cours programmée pour le moment.</p>
                        <p class="empty-suggestion">Revenez plus tard ou contactez vos enseignants pour plus d'informations.</p>
                    </div>
                @else
                        <div class="schedule-content">
                            <div class="view-info">
                                <div class="view-info-text">
                                    @if(request()->has('sort') && request('sort') == 'week')
                                        <i class="bi bi-calendar-week"></i>
                                        <span>Planning de la semaine en cours</span>
                                        <div class="current-week-tag">Semaine actuelle</div>
                                    @elseif(request()->has('sort_by_course'))
                                        <i class="bi bi-sort-alpha-down"></i>
                                        <span>Planning trié par cours</span>
                                    @else
                                        <i class="bi bi-calendar3"></i>
                                        <span>Planning complet</span>
                                    @endif
                                </div>
                            </div>

                            <div class="schedule-table-wrapper">
                                <table class="schedule-table" id="scheduleTable">
                                    <thead>
                                        <tr>
                                            <th>Cours</th>
                                            <th>Date & Heure de début</th>
                                            <th>Date & Heure de fin</th>
                                            <th>Enseignant</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($planning as $session)
                                                                @php
                                                                    $dateDebut = \Carbon\Carbon::parse($session->date_debut);
                                                                    $dateFin = \Carbon\Carbon::parse($session->date_fin);
                                                                    $duree = $dateDebut->diffInHours($dateFin) + ($dateDebut->diffInMinutes($dateFin) % 60 > 0 ? 1 : 0);
                                                                    $isToday = $dateDebut->isToday();
                                                                    $isPast = $dateFin->isPast();
                                                                    $isUpcoming = $dateDebut->isFuture();
                                                                    $status = $isToday ? 'today' : ($isPast ? 'past' : 'upcoming');
                                                                @endphp
                                                                <tr class="session-row {{ $status }}-session">
                                                                    <td class="course-cell">
                                                                        <div class="course-info">
                                                                            <div class="course-color-indicator"></div>
                                                                            <div class="course-details">
                                                                                <span class="course-name">{{ $session->intitule }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="datetime-cell">
                                                                        <div class="datetime-info">
                                                                            <div class="date">
                                                                                <i class="bi bi-calendar3"></i>
                                                                                <span>{{ $dateDebut->format('d/m/Y') }}</span>
                                                                            </div>
                                                                            <div class="time">
                                                                                <i class="bi bi-clock"></i>
                                                                                <span>{{ $dateDebut->format('H:i') }}</span>
                                                                            </div>
                                                                            @if($isToday)
                                                                                <div class="status-badge today">
                                                                                    <i class="bi bi-lightning"></i> Aujourd'hui
                                                                                </div>
                                                                            @elseif($isUpcoming)
                                                                                <div class="status-badge upcoming">
                                                                                    <i class="bi bi-calendar-plus"></i> À venir
                                                                                </div>
                                                                            @else
                                                                                <div class="status-badge past">
                                                                                    <i class="bi bi-calendar-check"></i> Passée
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                    <td class="datetime-cell">
                                                                        <div class="datetime-info">
                                                                            <div class="date">
                                                                                <i class="bi bi-calendar3"></i>
                                                                                <span>{{ $dateFin->format('d/m/Y') }}</span>
                                                                            </div>
                                                                            <div class="time">
                                                                                <i class="bi bi-clock-fill"></i>
                                                                                <span>{{ $dateFin->format('H:i') }}</span>
                                                                            </div>
                                                                            <div class="duration">
                                                                                <i class="bi bi-hourglass-split"></i>
                                                                                <span>{{ $duree }}h</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="teacher-cell">
                                                                        <div class="teacher-info">
                                                                            <div class="teacher-avatar">
                                                                                {{ substr($session->prenom, 0, 1) }}{{ substr($session->nom, 0, 1) }}
                                                                            </div>
                                                                            <span class="teacher-name">{{ $session->prenom }} {{ $session->nom }}</span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="pagination-wrapper">
                                {{ $planning->links() }}
                            </div>
                        </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('scheduleSearch');
            const table = document.getElementById('scheduleTable');
            const rows = table ? table.querySelectorAll('tbody tr') : [];

            if (searchInput && rows.length > 0) {
                searchInput.addEventListener('keyup', function () {
                    const query = this.value.toLowerCase();

                    rows.forEach(row => {
                        const courseName = row.querySelector('.course-name').textContent.toLowerCase();
                        const teacherName = row.querySelector('.teacher-name').textContent.toLowerCase();

                        if (courseName.includes(query) || teacherName.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            const courseNames = table ? Array.from(table.querySelectorAll('.course-name')).map(el => el.textContent) : [];
            const uniqueCourses = [...new Set(courseNames)];
            const colorIndicators = table ? table.querySelectorAll('.course-color-indicator') : [];

            const colorPalette = [
                '#4361ee', '#3a0ca3', '#7209b7', '#f72585', '#4cc9f0',
                '#4895ef', '#560bad', '#f15bb5', '#9b5de5', '#00bbf9',
                '#00f5d4', '#fee440', '#f15bb5', '#9b5de5', '#3a86ff'
            ];

            uniqueCourses.forEach((course, index) => {
                const color = colorPalette[index % colorPalette.length];

                Array.from(rows).forEach(row => {
                    const courseName = row.querySelector('.course-name').textContent;
                    if (courseName === course) {
                        const indicator = row.querySelector('.course-color-indicator');
                        if (indicator) {
                            indicator.style.backgroundColor = color;
                        }
                    }
                });
            });
        });
    </script>
@endsection