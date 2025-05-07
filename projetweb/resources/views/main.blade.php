@extends('layouts.modele')
@section('title', 'Accueil')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/main.css') }}">


    <div class="page-background"></div>

    <div class="homepage-container">

        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">moudeule.</h1>
                <p class="hero-subtitle">Notre université, notre plateforme</p>

                @auth
                    <div class="hero-actions">
                        @if(auth()->user()->type === 'etudiant')
                            <a href="{{ route('student.courses') }}" class="btn btn-primary btn-glow">
                                <span class="btn-text">Mes cours</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                            <a href="{{ route('planning.student_planning') }}" class="btn btn-secondary">
                                <i class="bi bi-calendar3"></i>
                                <span class="btn-text">Planning</span>
                            </a>
                        @elseif(auth()->user()->type === 'enseignant')
                            <a href="{{ route('student.courses') }}" class="btn btn-primary btn-glow">
                                <span class="btn-text">Cours assignés</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                            <a href="{{ route('planning.index') }}" class="btn btn-secondary">
                                <i class="bi bi-calendar3"></i>
                                <span class="btn-text">Planning</span>
                            </a>
                        @elseif(auth()->user()->type === 'admin')
                            <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-glow">
                                <span class="btn-text">Utilisateurs</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                            <a href="{{ route('cours.index') }}" class="btn btn-secondary">
                                <i class="bi bi-mortarboard"></i>
                                <span class="btn-text">Cours</span>
                            </a>
                        @endif
                    </div>
                @endauth
            </div>
        </section>


        <section class="features-section">
            <div class="section-header">
                <span class="section-tag">Fonctionnalités</span>
                <h2>Tout ce dont vous avez besoin</h2>
            </div>

            <div class="features-grid">
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                    <div class="feature-content">
                        <h3>Planning</h3>
                        <p>Visualisez et organisez votre emploi du temps facilement.</p>
                    </div>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="bi bi-book"></i>
                    </div>
                    <div class="feature-content">
                        <h3>Gestion des cours</h3>
                        <p>Accédez à vos ressources pédagogiques et suivez votre progression académique.</p>
                    </div>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="feature-content">
                        <h3>Collaboration</h3>
                        <p>Interagissez avec l'équipe pédagogique et les étudiants.</p>
                    </div>
                </div>
            </div>
        </section>


        <section class="news-section">
            <div class="section-header">
                <span class="section-tag">Actualités</span>
                <h2>Ce qu'il se passe sur le campus</h2>
            </div>

            <div class="news-container">
                <article class="news-card" data-aos="fade-right">
                    <div class="news-image">
                        <img src="{{ asset('images/images.jpg') }}" alt="Actualité du campus">
                        <div class="news-date">
                            <span class="day">19</span>
                            <span class="month">Avril</span>
                        </div>
                    </div>
                    <div class="news-content">
                        <h3>Tung Tung Sahur élu président étudiant</h3>
                        <p>Avec son programme tourné vers la tech éthique, il promet de révolutionner la vie de campus avec
                            des initiatives novatrices.</p>
                    </div>
                </article>

                <article class="news-card" data-aos="fade-left">
                    <div class="news-image">
                        <img src="{{ asset('images/cgt.jpg') }}" alt="Actualité budgétaire">
                        <div class="news-date">
                            <span class="day">15</span>
                            <span class="month">Avril</span>
                        </div>
                    </div>
                    <div class="news-content">
                        <h3>Réforme budgétaire universitaire</h3>
                        <p>Le Sénat adopte une réforme de réduction de 900 000 000 milliards d'euros d'économie impactant le
                            secteur éducatif.</p>
                    </div>
                </article>
            </div>
        </section>


        <section class="cta-section">
            <div class="cta-content">
                <h2>En savoir plus sur ce site ?</h2>
                <a href="{{ route('about') }}" class="btn btn-primary btn-large btn-glow">
                    <span class="btn-text">à propos</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="cta-shape"></div>
        </section>
    </div>


    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });

            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = 2000;
                const step = target / (duration / 16);

                let current = 0;
                const updateCounter = () => {
                    current += step;
                    if (current < target) {
                        counter.textContent = Math.ceil(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };
                const observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            updateCounter();
                            observer.unobserve(entry.target);
                        }
                    });
                });

                observer.observe(counter);
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