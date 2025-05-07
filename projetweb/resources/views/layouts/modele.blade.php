<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Moudeule</title> {{-- Changed name for consistency with home --}}

    <!-- Bootstrap + Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Animate.css (Optional, if used elsewhere) -->
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" /> --}}

    <!-- AOS CSS (Used in home.blade.php) -->
    <link href="https://unpkg.com/aos@next/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/pagination-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/components-alert.css') }}">

    {{-- Link to your combined layout CSS --}}
    <link rel="stylesheet" href="{{ asset('/css/layout.css') }}">
    {{-- Optionally link main.css if styles should be global, or merge relevant parts into layout.css --}}
    {{-- <link rel="stylesheet" href="{{ asset('/css/main.css') }}"> --}}

    {{-- DarkReader Script --}}
    <script src="https://unpkg.com/darkreader@4.9.58/darkreader.js"></script>
    <script>
        function toggleDarkMode() {
            const isDark = localStorage.getItem('dark-mode') === 'true';
            const moonIcon = '<i class="bi bi-moon-stars-fill"></i>'; // Updated Icon
            const sunIcon = '<i class="bi bi-sun-fill"></i>'; // Updated Icon

            if (isDark) {
                DarkReader.disable();
                localStorage.setItem('dark-mode', 'false');
                document.getElementById('darkModeToggle').innerHTML = moonIcon;
            } else {
                DarkReader.enable({
                    brightness: 100,
                    contrast: 90,
                    sepia: 10
                });
                localStorage.setItem('dark-mode', 'true');
                document.getElementById('darkModeToggle').innerHTML = sunIcon;
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            const moonIcon = '<i class="bi bi-moon-stars-fill"></i>'; // Updated Icon
            const sunIcon = '<i class="bi bi-sun-fill"></i>'; // Updated Icon
            if (localStorage.getItem('dark-mode') === 'true') {
                DarkReader.enable({
                    brightness: 100,
                    contrast: 90,
                    sepia: 10
                });
                document.getElementById('darkModeToggle').innerHTML = sunIcon;
            } else {
                // Ensure the correct icon is shown on initial load
                 document.getElementById('darkModeToggle').innerHTML = moonIcon;
            }
        });
    </script>
</head>

<body class="d-flex flex-column min-vh-100 @yield('body-class')">

    {{-- Use a container matching the homepage style --}}
    <div class="navbar-container">
        <nav class="navbar navbar-expand-lg navbar-light custom-navbar">
            <div class="container-fluid">
                {{-- Updated Brand to match home style --}}
                <a class="navbar-brand" href="{{ route('main') }}">
                     moudeule<span class="brand-dot">.</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list"></i> {{-- Using Bootstrap Icon --}}
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav align-items-center">
                        {{-- Added Icons to links --}}
                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('main')) active @endif" href="{{ route('main') }}">
                                <i class="bi bi-house-door-fill me-1"></i>Accueil
                            </a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('posts.index')) active @endif" href="{{ route('posts.index') }}">
                                <i class="bi bi-chat-dots-fill me-1"></i> Forum
                            </a>
                        </li>
                        @guest
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('login')) active @endif" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Connexion
                                </a>
                            </li>
                            <li class="nav-item">
                                {{-- Changed to a button style for emphasis --}}
                                <a class="btn btn-outline-primary btn-sm nav-btn ms-2" href="{{ route('register') }}">
                                    Inscription
                                </a>
                            </li>
                        @endguest
                        @auth
                            {{-- Added Planning link visibility based on role --}}
                            @if(Auth::user()->type === 'etudiant')
                                <li class="nav-item">
                                    <a class="nav-link @if(request()->routeIs('student.courses')) active @endif" href="{{ route('student.courses') }}">
                                        <i class="bi bi-book-fill me-1"></i>Mes Cours
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if(request()->routeIs('planning.student_planning')) active @endif" href="{{ route('planning.student_planning') }}">
                                        <i class="bi bi-calendar3 me-1"></i>Planning
                                    </a>
                                </li>
                            @elseif(Auth::user()->type === 'enseignant')
                                <li class="nav-item">
                                     <a class="nav-link @if(request()->routeIs('student.courses')) active @endif" href="{{ route('student.courses') }}">
                                         <i class="bi bi-journal-bookmark-fill me-1"></i>Cours Assignés
                                     </a>
                                 </li>
                                <li class="nav-item">
                                    <a class="nav-link @if(request()->routeIs('planning.index')) active @endif" href="{{ route('planning.index') }}">
                                        <i class="bi bi-calendar-week-fill me-1"></i>Planning
                                    </a>
                                </li>
                            @elseif(Auth::user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link @if(request()->routeIs('admin.home')) active @endif" href="{{ route('admin.home') }}">
                                        <i class="bi bi-person-gear me-1"></i>Admin
                                    </a>
                                </li>
                            @endif

                            {{-- User Dropdown --}}
                            <li class="nav-item dropdown ms-2">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle me-2"></i> {{ Auth::user()->prenom }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profil') }}">
                                            <i class="bi bi-person-vcard me-2"></i>Mon profil
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-box-arrow-right me-2"></i> Se déconnecter
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endauth
                        {{-- Dark Mode Toggle Button --}}
                        <li class="nav-item ms-2">
                            <button class="btn btn-outline-secondary btn-icon" onclick="toggleDarkMode()" id="darkModeToggle"
                                title="Basculer Dark/Light Mode">
                                <i class="bi bi-moon-stars-fill"></i> {{-- Default Icon --}}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    {{-- Main Content Area --}}
    <main class="page-content flex-fill">
        {{-- The container might be needed here or within the content view depending on page structure --}}
        <div class="container">
            @yield('content')
        </div>
    </main>

    {{-- Footer remains the same --}}
    <footer class="site-footer mt-auto">
        <div class="container">
            <div class="footer-content">
                {{-- Footer Brand --}}
                <a class="footer-brand" href="{{ route('main') }}">
                     moudeule<span class="brand-dot">.</span>
                </a>
                {{-- Copyright Text --}}
                <span class="copyright-text">&copy; {{ date('Y') }} Tous droits réservés.</span>
                {{-- Optional Links Area --}}
                {{-- <div class="footer-links">
                    <a href="#">Politique de confidentialité</a>
                    <a href="#">Conditions d'utilisation</a>
                </div> --}}
            </div>
        </div>
    </footer>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS (Used in home.blade.php) -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        // Initialize AOS if you use it on other pages too
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>
     @yield('scripts') {{-- Added section for page-specific scripts --}}
</body>
</html>