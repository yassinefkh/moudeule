@extends('layouts.modele')
@section('title', 'Tableau de bord - Administration')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/admin-home.css') }}">

    <div class="admin-dashboard">
        
        <div class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header">
                <div class="admin-logo">
                    <span class="admin-logo-text">moudeule</span>
                    <span class="admin-logo-dot">.</span>
                    <span class="admin-logo-admin">admin</span>
                </div>
                <button class="admin-sidebar-toggle d-md-none" id="sidebarToggle">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="admin-menu">
                <div class="admin-menu-section">
                    <h6 class="admin-menu-title">Principal</h6>
                    <ul class="admin-menu-items">
                        <li class="active">
                            <a href="{{ route('admin.home') }}">
                                <i class="bi bi-grid-1x2"></i>
                                <span>Tableau de bord</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}">
                                <i class="bi bi-people"></i>
                                <span>Utilisateurs</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.formations.index') }}">
                                <i class="bi bi-journal-text"></i>
                                <span>Formations</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cours.index') }}">
                                <i class="bi bi-book"></i>
                                <span>Cours</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('planning.index') }}">
                                <i class="bi bi-calendar2-check"></i>
                                <span>Plannings</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="admin-menu-section">
                    <h6 class="admin-menu-title">Actions rapides</h6>
                    <ul class="admin-menu-items">
                        <li>
                            <a href="{{ route('users.create') }}" class="create-action">
                                <i class="bi bi-person-plus"></i>
                                <span>Nouvel utilisateur</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.formations.create') }}" class="create-action">
                                <i class="bi bi-journal-plus"></i>
                                <span>Nouvelle formation</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="admin-sidebar-footer">
                <a href="{{ route('main') }}" class="admin-back-link">
                    <i class="bi bi-arrow-left-circle"></i>
                    <span>Retour au site</span>
                </a>
            </div>
        </div>

        
        <div class="admin-content">
            
            <div class="admin-top-bar">
                <button class="admin-sidebar-toggle d-md-none" id="sidebarOpen">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="admin-user-info">
                    <span class="admin-date">{{ date('d/m/Y') }}</span>
                    <div class="admin-user">
                        <span class="admin-welcome">Bonjour, <strong>{{ Auth::user()->prenom }}</strong></span>
                        <div class="admin-role-badge">Administrateur</div>
                    </div>
                </div>
            </div>

            
            <div class="admin-dashboard-content">
                <div class="admin-page-header">
                    <h1 class="admin-page-title">Tableau de bord</h1>
                    <p class="admin-page-description">Gérez l'ensemble de la plateforme depuis ce portail</p>
                </div>

                
                <div class="admin-stats-grid">
                    <div class="admin-stat-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="admin-stat-icon user-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="admin-stat-content">
                            <div class="admin-stat-value">{{ \App\Models\User::count() }}</div>
                            <div class="admin-stat-label">Utilisateurs</div>
                        </div>
                        <div class="admin-stat-chart user-chart"></div>
                    </div>
                    
                    <div class="admin-stat-card" data-aos="fade-up" data-aos-delay="150">
                        <div class="admin-stat-icon course-icon">
                            <i class="bi bi-book-fill"></i>
                        </div>
                        <div class="admin-stat-content">
                            <div class="admin-stat-value">{{ \App\Models\Cours::count() }}</div>
                            <div class="admin-stat-label">Cours</div>
                        </div>
                        <div class="admin-stat-chart course-chart"></div>
                    </div>
                    
                    <div class="admin-stat-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="admin-stat-icon formation-icon">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <div class="admin-stat-content">
                            <div class="admin-stat-value">{{ \App\Models\Formation::count() }}</div>
                            <div class="admin-stat-label">Formations</div>
                        </div>
                        <div class="admin-stat-chart formation-chart"></div>
                    </div>
                    
                    <div class="admin-stat-card" data-aos="fade-up" data-aos-delay="250">
                        <div class="admin-stat-icon session-icon">
                            <i class="bi bi-calendar2-week-fill"></i>
                        </div>
                        <div class="admin-stat-content">
                            <div class="admin-stat-value">{{ \App\Models\Planning::count() }}</div>
                            <div class="admin-stat-label">Séances</div>
                        </div>
                        <div class="admin-stat-chart session-chart"></div>
                    </div>
                </div>
                
                
                <div class="admin-modules-section">
                    <h2 class="admin-section-title">Modules d'administration</h2>
                    
                    <div class="admin-modules-grid">
                        
                        <div class="admin-module-card" data-aos="fade-up">
                            <div class="admin-module-header">
                                <div class="admin-module-icon">
                                    <i class="bi bi-people"></i>
                                </div>
                                <h3 class="admin-module-title">Gestion des utilisateurs</h3>
                            </div>
                            
                            <div class="admin-module-content">
                                <p>Gérez les comptes utilisateurs, modifiez leur type ou validez les inscriptions.</p>
                                
                                <div class="admin-module-stats">
                                    <div class="admin-module-stat">
                                        <div class="stat-label">Étudiants</div>
                                        <div class="stat-value">{{ \App\Models\User::where('type', 'etudiant')->count() }}</div>
                                    </div>
                                    <div class="admin-module-stat">
                                        <div class="stat-label">Enseignants</div>
                                        <div class="stat-value">{{ \App\Models\User::where('type', 'enseignant')->count() }}</div>
                                    </div>
                                    <div class="admin-module-stat">
                                        <div class="stat-label">Admins</div>
                                        <div class="stat-value">{{ \App\Models\User::where('type', 'admin')->count() }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="admin-module-actions">
                                <a href="{{ route('admin.users.index') }}" class="admin-card-btn btn-primary">
                                    <span class="btn-text">Liste des utilisateurs</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                                <a href="{{ route('users.create') }}" class="admin-card-btn btn-accent">
                                    <span class="btn-text">Ajouter</span>
                                    <i class="bi bi-plus-lg"></i>
                                </a>
                            </div>
                        </div>
                        
                        
                        <div class="admin-module-card" data-aos="fade-up" data-aos-delay="100">
                            <div class="admin-module-header">
                                <div class="admin-module-icon">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <h3 class="admin-module-title">Formations académiques</h3>
                            </div>
                            
                            <div class="admin-module-content">
                                <p>Consultez, modifiez ou créez des formations pour les différents parcours académiques.</p>
                                
                                <div class="admin-module-info">
                                    <div class="info-item">
                                        <i class="bi bi-mortarboard"></i>
                                        <span>{{ \App\Models\Formation::count() }} formations disponibles</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="admin-module-actions">
                                <a href="{{ route('admin.formations.index') }}" class="admin-card-btn btn-primary">
                                    <span class="btn-text">Liste des formations</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                                <a href="{{ route('admin.formations.create') }}" class="admin-card-btn btn-accent">
                                    <span class="btn-text">Ajouter</span>
                                    <i class="bi bi-plus-lg"></i>
                                </a>
                            </div>
                        </div>
                        
                        
                        <div class="admin-module-card" data-aos="fade-up" data-aos-delay="150">
                            <div class="admin-module-header">
                                <div class="admin-module-icon">
                                    <i class="bi bi-book"></i>
                                </div>
                                <h3 class="admin-module-title">Gestion des cours</h3>
                            </div>
                            
                            <div class="admin-module-content">
                                <p>Consultez et modifiez les cours disponibles pour chaque formation.</p>
                                
                                <div class="admin-module-info">
                                    <div class="info-item">
                                        <i class="bi bi-book"></i>
                                        <span>{{ \App\Models\Cours::count() }} cours enregistrés</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="admin-module-actions">
                                <a href="{{ route('cours.index') }}" class="admin-card-btn btn-primary">
                                    <span class="btn-text">Gérer les cours</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        
                        
                        <div class="admin-module-card" data-aos="fade-up" data-aos-delay="200">
                            <div class="admin-module-header">
                                <div class="admin-module-icon">
                                    <i class="bi bi-calendar2-check"></i>
                                </div>
                                <h3 class="admin-module-title">Planning & Séances</h3>
                            </div>
                            
                            <div class="admin-module-content">
                                <p>Visualisez et gérez toutes les séances de cours et plannings des formations.</p>
                                
                                <div class="admin-module-info">
                                    <div class="info-item">
                                        <i class="bi bi-calendar2-week"></i>
                                        <span>{{ \App\Models\Planning::whereDate('date_debut', '>=', date('Y-m-d'))->count() }} séances à venir</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="admin-module-actions">
                                <a href="{{ route('planning.index') }}" class="admin-card-btn btn-secondary">
                                    <span class="btn-text">Accéder au planning</span>
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true,
                offset: 50
            });
            
            
            const sidebar = document.getElementById('adminSidebar');
            const sidebarOpen = document.getElementById('sidebarOpen');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            sidebarOpen.addEventListener('click', function() {
                sidebar.classList.add('active');
            });
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.remove('active');
            });
            
            
            const buttons = document.querySelectorAll('.admin-card-btn');
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