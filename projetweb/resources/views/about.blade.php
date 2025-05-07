@extends('layouts.modele')
@section('title', 'À propos du projet')
@section('content')

    <link rel="stylesheet" href="{{ asset('/css/about.css') }}">

    <div class="about-container">
        <header class="about-header">
            <div class="about-header-content">
                <h1>Projet Web - Plateforme Pédagogique</h1>
                <p class="tagline">Programmation Web - Année universitaire 2024/2025</p>
            </div>
        </header>

        <section class="about-authors">
    <div class="about-card authors-card">
        <h2>Équipe du projet</h2>
        
        <div class="authors-list">
            <div class="author-block">
                <h3>Yassine FEKIH HASSEN</h3>
                <p>M1 VMI</p>
            </div>

            <div class="author-block">
                <h3>Auguste CALMANOVIC-PLESCOFF</h3>
                <p>M1 VMI</p>
            </div>

            <div class="author-block">
                <h3>Titouan BRIERRE</h3>
                <p>M1 VMI</p>
            </div>

            <div class="author-block">
                <h3>Anais KADIC</h3>
                <p>M1 VMI</p>
            </div>
        </div>

        <div class="professor" style="margin-top: 2rem;">
            <h3>Enseignant :</h3>
            <p>M. Antoine MARTIN</p>
        </div>
    </div>
</section>



        <section class="about-project">
            <h2>Présentation du projet</h2>
            <div class="project-description">
                <p>Dans le cadre du cours de Programmation Web, nous avons développé une plateforme pédagogique qui
                    pourrait répondre aux besoins des universités. Ce projet met en application les
                    concepts avancés enseignés (ou non) durant le cours, notamment :</p>

                <ul class="project-highlights">
                    <li>Architecture MVC avec Laravel</li>
                    <li>Gestion avancée des utilisateurs et des rôles</li>
                    <li>Interface responsive et adaptative</li>
                    <li>Sécurisation des données et des accès</li>
                    <li>Gestion d'une base de données relationnelle</li>
                </ul>
            </div>
        </section>

        <section class="about-features">
            <h2>Fonctionnalités implémentées</h2>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h3>Système d'authentification</h3>
                    <p>Inscription, connexion, gestion des rôles (étudiant, enseignant, administrateur) avec validation des
                        comptes par l'administrateur.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-book"></i>
                    </div>
                    <h3>Gestion des cours</h3>
                    <p>Création de cours par les enseignants, organisation par sections, téléchargement de documents
                        pédagogiques sécurisés.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                    <h3>Planning interactif</h3>
                    <p>Calendrier des cours adapté au profil utilisateur, avec vue hebdomadaire et vue liste pour une
                        meilleure organisation.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h3>Système d'évaluation</h3>
                    <p>Gestion des contrôles avec coefficients, saisie des notes par les enseignants, calcul automatique des
                        moyennes pour les étudiants.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <h3>Forum et interactions</h3>
                    <p>Système de forum avec publications, commentaires et réactions.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3>Administration</h3>
                    <p>Interface complète pour la gestion des utilisateurs, des formations et la supervision de l'ensemble
                        des activités pédagogiques.</p>
                </div>
            </div>
        </section>

        <section class="about-challenges">
            <h2>Défis techniques relevés</h2>

            <div class="challenges-container">
                <div class="challenge-item">
                    <div class="challenge-icon">
                        <i class="bi bi-database-lock"></i>
                    </div>
                    <div class="challenge-content">
                        <h3>Relations complexes en base de données</h3>
                        <p>Mise en place d'une structure relationnelle élaborée pour gérer les inscriptions aux cours, les
                            notes, et les emplois du temps sans redondance de données.</p>
                    </div>
                </div>

                <div class="challenge-item">
                    <div class="challenge-icon">
                        <i class="bi bi-window-sidebar"></i>
                    </div>
                    <div class="challenge-content">
                        <h3>Interface utilisateur adaptative</h3>
                        <p>Développement d'une interface réactive qui s'adapte aux différents rôles d'utilisateurs en
                            affichant dynamiquement les fonctionnalités appropriées.</p>
                    </div>
                </div>

                <div class="challenge-item">
                    <div class="challenge-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <div class="challenge-content">
                        <h3>Gestion des autorisations</h3>
                        <p>Implémentation d'un système robuste de contrôle d'accès basé sur les rôles pour protéger les
                            ressources et garantir la confidentialité des données.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-tech">
            <h2>Technologies et méthodes utilisées</h2>

            <div class="tech-grid">
                <div class="tech-category">
                    <h3>Backend</h3>
                    <ul class="tech-list">
                        <li><i class="bi bi-filetype-php"></i> PHP 8.1</li>
                        <li><i class="bi bi-box"></i> Laravel 10</li>
                        <li><i class="bi bi-database"></i> MySQL</li>
                        <li><i class="bi bi-shield-check"></i> Authentication middleware</li>
                    </ul>
                </div>

                <div class="tech-category">
                    <h3>Frontend</h3>
                    <ul class="tech-list">
                        <li><i class="bi bi-filetype-html"></i> HTML5</li>
                        <li><i class="bi bi-filetype-css"></i> CSS3</li>
                        <li><i class="bi bi-filetype-js"></i> JavaScript</li>
                        <li><i class="bi bi-bootstrap"></i> Bootstrap 5</li>
                    </ul>
                </div>

                <div class="tech-category">
                    <h3>Outils et méthodes</h3>
                    <ul class="tech-list">
                        <li><i class="bi bi-git"></i>GitHub</li>
                        <li><i class="bi bi-diagram-3"></i> Architecture MVC</li>
                        <li><i class="bi bi-check-circle"></i> Validation des données</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="about-learning">
            <h2>Compétences développées</h2>

            <div class="learning-grid">
                <div class="learning-item">
                    <div class="learning-icon">
                        <i class="bi bi-layers"></i>
                    </div>
                    <h3>Architecture logicielle</h3>
                    <p>Approfondissement des principes MVC et structuration d'une application web complexe avec Laravel.</p>
                </div>

                <div class="learning-item">
                    <div class="learning-icon">
                        <i class="bi bi-shield-fill"></i>
                    </div>
                    <h3>Sécurité web</h3>
                    <p>Implémentation des bonnes pratiques de sécurité : validations côté serveur, protection contre les
                        injections SQL et gestion sécurisée des sessions.</p>
                </div>

                <div class="learning-item">
                    <div class="learning-icon">
                        <i class="bi bi-ui-checks"></i>
                    </div>
                    <h3>Expérience utilisateur</h3>
                    <p>Conception d'interfaces intuitives et fonctionnelles adaptées aux besoins spécifiques de chaque type
                        d'utilisateur.</p>
                </div>

                <div class="learning-item">
                    <div class="learning-icon">
                        <i class="bi bi-database-gear"></i>
                    </div>
                    <h3>Conception de base de données</h3>
                    <p>Élaboration d'un modèle relationnel complexe optimisé pour les requêtes fréquentes et l'intégrité des
                        données.</p>
                </div>
            </div>
        </section>

        <section class="project-evaluation">
            <h2>Auto-évaluation et perspectives</h2>

            <div class="evaluation-content">
               

                <div class="strengths-improvements">


                    <div class="improvements">
                        <h3>Améliorations futures</h3>
                        <ul>
                            <li>
                                <strong>Modularisation du backend en microservices.</strong>
                            </li>

                            <li>
                                <strong>Ajout de notifications en temps réel :</strong> utiliser Laravel Echo ou Pusher pour
                                notifier les utilisateurs lors de la publication d’une annonce ou d’un nouveau document.
                            </li>
                            <li>
                                <strong>Système d'audit et d'historique :</strong> journalisation des actions importantes
                                pour assurer une traçabilité complète (création, modification, suppression de contenus).
                            </li>
                            <li>
                                <strong>Tests automatisés :</strong> couverture du code backend par des tests unitaires et
                                d’intégration.
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </section>

        <section class="about-summary">
            <div class="summary-card">
                <h2>Conclusion</h2>
                <p>Ce projet réalisé dans le cadre de l’UE de programmation web nous a permis de consolider notre
                    compréhension du modèle MVC avec Laravel, tout en nous familiarisant avec la mise en place
                    d’architectures RESTful pour une API propre et évolutive.</p>
                <p>Nous avons également approfondi des aspects essentiels comme la sécurisation des routes et des accès, la
                    gestion des rôles utilisateurs, ainsi que l’intégration et la validation de données côté serveur.</p>
                <p>Merci à M. MARTIN pour son enseignement.</p>
                <div class="date">Avril 2024</div>
            </div>

        </section>
    </div>

@endsection