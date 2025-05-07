@extends('layouts.modele')
@section('title', 'Profil')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/user-profile.css') }}">

    <div class="page-background profile-background"></div>

    <div class="profile-container">
        <div class="profile-header" data-aos="fade-down">
            <h1 class="profile-page-title">Mon Profil</h1>
            <span class="profile-subtitle">Gérez vos informations personnelles</span>
        </div>

        <div class="profile-grid">

            <div class="profile-sidebar" data-aos="fade-right">
                <div class="profile-card">
                    <div class="profile-avatar">
                        {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1)) }}
                    </div>

                    <div class="profile-user-info">
                        <h2 class="profile-name">{{ $user->prenom }} {{ $user->nom }}</h2>
                        <div class="profile-badge">{{ ucfirst($user->type) }}</div>
                    </div>

                    <div class="profile-divider"></div>

                    <form action="{{ route('user.update', $user->id) }}" method="POST" class="profile-form">
                        @csrf
                        @method('PUT')

                        <div class="profile-info-item">
                            <span class="profile-info-label">Login</span>
                            <span class="profile-info-value">{{ $user->login }}</span>
                        </div>

                        <div class="profile-form-group">
                            <label for="nom" class="profile-form-label">Nom</label>
                            <input type="text" class="profile-form-input" id="nom" name="nom" value="{{ $user->nom }}">
                        </div>

                        <div class="profile-form-group">
                            <label for="prenom" class="profile-form-label">Prénom</label>
                            <input type="text" class="profile-form-input" id="prenom" name="prenom"
                                value="{{ $user->prenom }}">
                        </div>

                        <button type="submit" class="btn btn-primary btn-glow profile-update-btn">
                            <span class="btn-text">Mettre à jour</span>
                            <i class="bi bi-check-circle"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="profile-content" data-aos="fade-left">
                @if ($user->type == 'etudiant')
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <div class="profile-card-icon">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <h3 class="profile-card-title">Ma formation</h3>
                        </div>

                        <div class="profile-card-body">
                            <div class="profile-info-group">
                                <span class="profile-info-label">Formation</span>
                                <span class="profile-info-value">
                                    @if($user->formation)
                                        <a href="{{ route('formations.show', $user->formation->id) }}" class="profile-link">
                                            <span>{{ $user->formation->intitule }}</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">Non renseignée</span>
                                    @endif
                                </span>
                            </div>

                            <div class="profile-info-group">
                                <span class="profile-info-label">Université</span>
                                <span class="profile-info-value">Université Paris Cité</span>
                            </div>
                        </div>
                    </div>

         
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <div class="profile-card-icon">
                                <i class="bi bi-clipboard-check-fill"></i>
                            </div>
                            <h3 class="profile-card-title">Mes résultats</h3>
                        </div>

                        <div class="profile-card-body">
                  
                           
                                <div class="grades-overview">
                                    <div class="overall-average">
                                        <div class="average-circle">
                                            @php 
                                                $moyenneGenerale = $user->moyenneGenerale();
                                            @endphp
                                            <span class="average-value {{ $moyenneGenerale !== null && $moyenneGenerale >= 10 ? 'passing' : 'failing' }}">
                                                {{ $moyenneGenerale !== null ? number_format($moyenneGenerale, 1) : 'N/A' }}
                                            </span>
                                            <span class="average-label">Moyenne générale</span>
                                        </div>
                                    </div>
                                </div>

                            <div class="course-grades">
                                <h4 class="grades-section-title">Détail par cours</h4>
                                
                                @if ($user->courses->count())
                                    <div class="grades-table-wrapper">
                                        <table class="grades-table">
                                            <thead>
                                                <tr>
                                                    <th>Cours</th>
                                                    <th>Moyenne</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($user->courses as $course)
                                                    @php 
                                                        $moyenne = $course->moyenneForUser($user->id);
                                                    @endphp
                                                    <tr>
                                                        <td class="course-name-cell">
                                                            <span class="course-name-grades">{{ $course->intitule }}</span>
                                                        </td>
                                                        <td class="course-average-cell">
                                                            <span class="course-average {{ is_numeric($moyenne) && $moyenne >= 10 ? 'passing' : 'failing' }}">
                                                                {{ is_numeric($moyenne) ? number_format($moyenne, 1) : 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td class="course-action-cell">
                                                            <a href="{{ route('cours.show', $course->id) }}" class="btn-view-grades">
                                                                <i class="bi bi-eye"></i> <span>Détails</span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="bi bi-clipboard-x"></i>
                                        </div>
                                        <p class="empty-state-message">Aucune note disponible pour le moment.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="profile-card">
                        <div class="profile-card-header">
                            <div class="profile-card-icon">
                                <i class="bi bi-journal-bookmark-fill"></i>
                            </div>
                            <h3 class="profile-card-title">Mes cours</h3>
                        </div>

                        <div class="profile-card-body">
                            @if ($user->courses->count())
                                <div class="courses-grid">
                                    @foreach ($user->courses as $course)
                                        <div class="course-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                                            <h4 class="course-title">{{ $course->intitule }}</h4>
                                            <div class="course-details">
                                                <p class="course-teacher">
                                                    <i class="bi bi-person-fill"></i>
                                                    <span>{{ $course->user->prenom }} {{ $course->user->nom }}</span>
                                                </p>
                                                <p class="course-id">
                                                    <i class="bi bi-hash"></i>
                                                    <span>ID: {{ $course->id }}</span>
                                                </p>
                                            </div>
                                            <a href="{{ route('cours.show', $course->id) }}" class="course-link">
                                                <span>Voir le cours</span>
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-journal-x"></i>
                                    </div>
                                    <p class="empty-state-message">Vous n'êtes inscrit à aucun cours actuellement.</p>
                                    <a href="#" class="btn btn-secondary">
                                        <span class="btn-text">Explorer les cours</span>
                                        <i class="bi bi-search"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($user->type == 'enseignant')
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <div class="profile-card-icon">
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>
                            <h3 class="profile-card-title">Gestion du planning</h3>
                        </div>

                        <div class="profile-card-body">
                            <p class="profile-card-description">Consultez et gérez les séances d'enseignement dans votre emploi
                                du temps.</p>
                            <a href="{{ route('planning.index') }}" class="btn btn-primary btn-glow mt-3">
                                <span class="btn-text">Accéder au planning</span>
                                <i class="bi bi-calendar-week"></i>
                            </a>
                        </div>
                    </div>

                    <div class="profile-card">
                        <div class="profile-card-header">
                            <div class="profile-card-icon">
                                <i class="bi bi-journal-richtext"></i>
                            </div>
                            <h3 class="profile-card-title">Cours assignés</h3>
                        </div>

                        <div class="profile-card-body">
                            @if ($user->assignedCourses->count())
                                <div class="courses-grid">
                                    @foreach ($user->assignedCourses as $course)
                                        <div class="course-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                                            <h4 class="course-title">{{ $course->intitule }}</h4>
                                            <div class="course-details">
                                                <p class="course-teacher">
                                                    <i class="bi bi-mortarboard"></i>
                                                    <span>{{ $course->formation->intitule }}</span>
                                                </p>
                                                <p class="course-id">
                                                    <i class="bi bi-hash"></i>
                                                    <span>ID: {{ $course->id }}</span>
                                                </p>
                                            </div>
                                            <a href="{{ route('cours.show', $course->id) }}" class="course-link">
                                                <span>Gérer ce cours</span>
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-journal-x"></i>
                                    </div>
                                    <p class="empty-state-message">Aucun cours ne vous est assigné actuellement.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            AOS.init({
                duration: 800,
                once: true,
                offset: 50
            });

            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('mousemove', e => {
                    const rect = button.getBoundingClientRect();
                    const x = ((e.clientX - rect.left) / button.clientWidth) * 100;
                    const y = ((e.clientY - rect.top) / button.clientHeight) * 100;
                    button.style.setProperty('--mouse-x', `${x}%`);
                    button.style.setProperty('--mouse-y', `${y}%`);
                });
            });
        });
    </script>
@endsection