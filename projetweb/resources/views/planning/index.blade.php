@extends('layouts.modele')
@section('title', 'Planning des cours')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/planning-index.css') }}">

    <div class="page-background planning-background"></div>

    <div class="planning-dashboard">
        <div class="dashboard-container">
            <div class="dashboard-header" data-aos="fade-down">
                <div class="header-content">
                    <div class="header-titles">
                        <h1 class="dashboard-title">Planning des cours</h1>
                        <p class="dashboard-subtitle">Gérez facilement vos séances d'enseignement</p>
                    </div>

                    @if (Auth::user()->type === 'enseignant' || Auth::user()->isAdmin())
                        <a href="{{ route('planning.create') }}" class="btn-create">
                            <div class="btn-icon"><i class="bi bi-plus-lg"></i></div>
                            <span>Nouvelle séance</span>
                        </a>
                    @endif
                </div>

                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $planning->total() }}</div>
                            <div class="stat-label">Séances programmées</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $planning->where('date_debut', '>=', now())->count() }}</div>
                            <div class="stat-label">À venir</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-graph-up"></i></div>
                        <div class="stat-content">
                            @php
                                $totalDurationInMinutes = 0;
                                foreach ($planning as $session) {
                                    $startTime = \Carbon\Carbon::parse($session->date_debut);
                                    $endTime = \Carbon\Carbon::parse($session->date_fin);
                                    $totalDurationInMinutes += $startTime->diffInMinutes($endTime);
                                }
                                $totalHours = floor($totalDurationInMinutes / 60);
                            @endphp
                            <div class="stat-value">{{ $totalHours }}h</div>
                            <div class="stat-label">Heures totales</div>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif

            @if (session('error'))
                <x-alert type="danger" :message="session('error')" />
            @endif

            @if (session('warning'))
                <x-alert type="warning" :message="session('warning')" />
            @endif

            @if (session('info'))
                <x-alert type="info" :message="session('info')" />
            @endif

            <div class="dashboard-filters" data-aos="fade-up">
                <div class="filters-wrapper">
                    <div class="filter-group">
                        <a href="{{ route('planning.index') }}"
                            class="filter-link {{ !request()->has('week') && !request()->has('sort_by_course') ? 'active' : '' }}">
                            <i class="bi bi-grid-3x3-gap"></i> Toutes les séances
                        </a>
                        <a href="{{ route('planning.index', ['week' => 'current']) }}"
                            class="filter-link {{ request()->has('week') ? 'active' : '' }}">
                            <i class="bi bi-calendar-week"></i> Cette semaine
                        </a>
                        <a href="{{ route('planning.index', ['sort_by_course' => 1]) }}"
                            class="filter-link {{ request()->has('sort_by_course') ? 'active' : '' }}">
                            <i class="bi bi-sort-alpha-down"></i> Par cours
                        </a>
                    </div>

                    <div class="filter-group secondary">
                        @if (Auth::user()->type === 'etudiant')
                            <a href="{{ route('planning.student_planning') }}" class="filter-link">
                                <i class="bi bi-calendar4-week"></i> Vue calendrier
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="dashboard-content" data-aos="fade-up" data-aos-delay="100">
                @if ($planning->isEmpty())
                    <div class="empty-state">
                        <div class="empty-illustration">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <h3 class="empty-title">Aucune séance programmée</h3>
                        <p class="empty-description">Il n'y a actuellement aucune séance de cours dans le planning.</p>

                        @if (Auth::user()->type === 'enseignant' || Auth::user()->isAdmin())
                            <a href="{{ route('planning.create') }}" class="btn-create-first">
                                <i class="bi bi-plus-circle"></i>
                                <span>Programmer une séance</span>
                            </a>
                        @endif
                    </div>
                @else
                        <div class="sessions-grid">
                            @foreach ($planning as $session)
                                        @php
                                            $isPast = \Carbon\Carbon::parse($session->date_fin)->isPast();
                                            $isToday = \Carbon\Carbon::parse($session->date_debut)->isToday();
                                            $isFuture = \Carbon\Carbon::parse($session->date_debut)->isFuture();
                                            $statusClass = $isPast ? 'past' : ($isToday ? 'today' : 'upcoming');
                                        @endphp
                                        <div class="session-card {{ $statusClass }}" data-aos="fade-up"
                                            data-aos-delay="{{ $loop->index * 50 }}">
                                            <div class="session-header">
                                                <div class="session-status">
                                                    @if($isPast)
                                                        <span class="status past">Terminé</span>
                                                    @elseif($isToday)
                                                        <span class="status today">Aujourd'hui</span>
                                                    @else
                                                        <span class="status upcoming">À venir</span>
                                                    @endif
                                                </div>

                                                @if (Auth::user()->type === 'enseignant' || Auth::user()->isAdmin())
                                                    <div class="session-actions">
                                                        <div class="dropdown">
                                                            <button class="btn-menu" id="dropdownMenu{{ $session->id }}" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end"
                                                                aria-labelledby="dropdownMenu{{ $session->id }}">
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('planning.edit', $session->id) }}">
                                                                        <i class="bi bi-pencil"></i> Modifier
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <form action="{{ route('planning.destroy', $session->id) }}" method="POST"
                                                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette séance ?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="dropdown-item text-danger" type="submit">
                                                                            <i class="bi bi-trash"></i> Supprimer
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <h3 class="session-title">{{ $session->intitule }}</h3>

                                            <div class="session-details">
                                                <div class="detail-item">
                                                    <div class="detail-icon"><i class="bi bi-calendar-date"></i></div>
                                                    <div class="detail-content">
                                                        {{ \Carbon\Carbon::parse($session->date_debut)->format('d/m/Y') }}
                                                    </div>
                                                </div>

                                                <div class="detail-item">
                                                    <div class="detail-icon"><i class="bi bi-clock"></i></div>
                                                    <div class="detail-content">
                                                        {{ \Carbon\Carbon::parse($session->date_debut)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($session->date_fin)->format('H:i') }}
                                                    </div>
                                                </div>

                                                <div class="detail-item">
                                                    <div class="detail-icon"><i class="bi bi-person"></i></div>
                                                    <div class="detail-content teacher-content">
                                                        <div class="teacher-avatar">
                                                            {{ strtoupper(substr($session->prenom, 0, 1) . substr($session->nom, 0, 1)) }}
                                                        </div>
                                                        <span class="teacher-name">{{ $session->prenom }} {{ $session->nom }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="session-duration">
                                                <i class="bi bi-hourglass-split"></i>
                                                @php

                                                    $startTime = \Carbon\Carbon::parse($session->date_debut);
                                                    $endTime = \Carbon\Carbon::parse($session->date_fin);
                                                    $durationInMinutes = $startTime->diffInMinutes($endTime);
                                                    $hours = floor($durationInMinutes / 60);
                                                    $minutes = $durationInMinutes % 60;

                                                    $durationText = $hours . 'h';
                                                    if ($minutes > 0) {
                                                        $durationText .= $minutes;
                                                    }
                                                @endphp
                                                <span>{{ $durationText }}</span>
                                            </div>
                                        </div>
                            @endforeach
                        </div>

                        <div class="pagination-container">
                            {{ $planning->links() }}
                        </div>
                @endif
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

            const filtersToggle = document.querySelector('.filters-toggle');
            if (filtersToggle) {
                filtersToggle.addEventListener('click', function () {
                    document.querySelector('.filters-wrapper').classList.toggle('show');
                });
            }
        });
    </script>
@endsection