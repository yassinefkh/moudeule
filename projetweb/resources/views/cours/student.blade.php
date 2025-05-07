@extends('layouts.modele')
@section('title', 'Liste des cours de ma formation')

@section('content')

    <link rel="stylesheet" href="{{ asset('/css/cours-student.css') }}">

    <section class="courses-page">
        <div class="courses-container">
            <div class="page-header">
                <h1 class="page-title">Catalogue de cours</h1>
                <p class="page-subtitle">
                    @if (auth()->user()->type === 'enseignant')
                        Voici les cours que vous gérez en tant qu'enseignant.
                    @else
                        Découvrez tous les cours disponibles dans votre formation et inscrivez-vous pour accéder aux contenus.
                    @endif
                </p>
            </div>

            @php
                $isTeacher = auth()->user()->type === 'enseignant';
                $teacherCourses = $isTeacher ? auth()->user()->assignedCourses()->with(['formation', 'users'])->get() : collect();
            @endphp

            @if ($isTeacher && $teacherCourses->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-book empty-icon"></i>
                    <h3 class="empty-title">Aucun cours à gérer</h3>
                    <p class="empty-description">Vous ne gérez actuellement aucun cours.</p>
                </div>
            @elseif (!$isTeacher && $courses->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-book empty-icon"></i>
                    <h3 class="empty-title">Aucun cours disponible</h3>
                    <p class="empty-description">Il n'y a actuellement aucun cours disponible dans votre formation.</p>
                </div>
            @else
                @if (!$isTeacher)
                    <form method="GET" action="{{ route('student.courses') }}" class="search-wrapper">
                        <div class="search-box">
                            <input type="text" class="search-input" name="search" placeholder="Rechercher un cours par mot-clé"
                                value="{{ request('search') }}">
                            <button type="submit" class="search-button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                @endif

                <div class="courses-grid">
                    @foreach (($isTeacher ? $teacherCourses : $courses) as $course)
                        <div class="course-card">
                            <div class="course-header">
                                <a href="{{ route('cours.show', $course->id) }}">
                                    <h3 class="course-title">{{ $course->intitule }}</h3>
                                </a>
                                <span class="course-formation">{{ $course->formation->intitule }}</span>
                            </div>
                            <div class="course-body">
                                <div class="course-info">
                                    <div class="info-item">
                                        <span class="info-icon teacher">
                                            <i class="bi bi-person-badge-fill"></i>
                                        </span>
                                        <span>{{ $course->user->prenom }} {{ $course->user->nom }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-icon id">
                                            <i class="bi bi-hash"></i>
                                        </span>
                                        <span>Cours ID: {{ $course->id }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-icon">
                                            <i class="bi bi-people-fill"></i>
                                        </span>
                                        <span>{{ $course->users->count() }} étudiant{{ $course->users->count() > 1 ? 's' : '' }}
                                            inscrit{{ $course->users->count() > 1 ? 's' : '' }}</span>
                                    </div>
                                </div>

                                <div class="course-actions">
                                    @auth
                                        @if ($isTeacher || auth()->user()->courses->contains($course))
                                            <a href="{{ route('cours.show', $course->id) }}" class="btn-course btn-enroll" ,
                                                style="text-decoration:none">
                                                Voir le cours
                                            </a>
                                        @endif
                                    @endauth


                                    @if (!$isTeacher)
                                        @if (auth()->user()->courses->contains($course))
                                            <form method="POST" action="{{ route('student.unenroll', $course->id) }}" style="flex: 1;">
                                                @csrf
                                                <button type="submit" class="btn-course btn-unenroll" style="width: 100%;">
                                                    <i class="fas fa-user-minus"></i> Se désinscrire
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('student.enroll', $course->id) }}" style="flex: 1;">
                                                @csrf
                                                <button type="submit" class="btn-course btn-enroll" style="width: 100%;">
                                                    <i class="fas fa-user-plus"></i> S'inscrire
                                                </button>
                                            </form>
                                            <button class="btn-course btn-not-enrolled" disabled style="flex: 1;">
                                                <i class="fas fa-times"></i> Non inscrit
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (!$isTeacher)
                    <div class="pagination-wrapper">
                        {{ $courses->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection