@extends('layouts.modele')
@section('title', 'Inscription')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/auth-register.css') }}">

    <div class="page-background auth-background"></div>

    <div class="auth-container">
        <div class="auth-header">
            <div class="auth-header-content">
                <h1 class="auth-title">
                    <i class="bi bi-person-plus"></i>
                    <span>Créer un compte</span>
                </h1>
                <p class="auth-subtitle">Rejoignez notre plateforme pour accéder à tous nos services</p>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger fade-in">
                <div class="alert-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="alert-content">
                    <p>{{ session('error') }}</p>
                </div>
                <button class="alert-close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        <div class="auth-card">
            <div class="auth-form-container">
                <form method="POST" action="{{ route('register') }}" class="auth-form">
                    @csrf

                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="bi bi-person-vcard"></i>
                            <span>Informations personnelles</span>
                        </h3>
                        <p class="section-description">Veuillez renseigner vos informations pour créer votre compte.</p>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom</label>
                                <div class="input-container">
                                    <div class="input-wrapper">

                                        <input type="text" id="nom" name="nom"
                                            class="form-input @error('nom') is-invalid @enderror" placeholder="Votre nom"
                                            value="{{ old('nom') }}" autocomplete="family-name">
                                    </div>
                                    @error('nom')
                                        <div class="error-message">
                                            <i class="bi bi-exclamation-circle"></i>
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="prenom" class="form-label">Prénom</label>
                                <div class="input-container">
                                    <div class="input-wrapper">

                                        <input type="text" id="prenom" name="prenom"
                                            class="form-input @error('prenom') is-invalid @enderror"
                                            placeholder="Votre prénom" value="{{ old('prenom') }}"
                                            autocomplete="given-name">
                                    </div>
                                    @error('prenom')
                                        <div class="error-message">
                                            <i class="bi bi-exclamation-circle"></i>
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="login" class="form-label">Identifiant</label>
                            <div class="input-container">
                                <div class="input-wrapper">

                                    <input type="text" id="login" name="login"
                                        class="form-input @error('login') is-invalid @enderror"
                                        placeholder="Choisissez votre identifiant de connexion" value="{{ old('login') }}"
                                        autocomplete="username">
                                </div>
                                @error('login')
                                    <div class="error-message">
                                        <i class="bi bi-exclamation-circle"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mdp" class="form-label">Mot de passe</label>
                            <div class="password-input-container">
                                <div class="password-input-wrapper">
                                    <i class="bi bi-lock"></i>
                                    <input type="password" id="mdp" name="mdp"
                                        class="form-input @error('mdp') is-invalid @enderror"
                                        placeholder="Créez un mot de passe sécurisé" autocomplete="new-password">

                                </div>
                                @error('mdp')
                                    <div class="error-message">
                                        <i class="bi bi-exclamation-circle"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-section-divider"></div>

                        <h3 class="section-title">
                            <i class="bi bi-person-badge"></i>
                            <span>Type de profil</span>
                        </h3>
                        <p class="section-description">Sélectionnez votre type de profil pour définir vos accès sur la
                            plateforme.</p>

                        <div class="form-group">
                            <div class="profile-type-container">
                                <div class="profile-type-options">
                                    <label class="profile-type-option">
                                        <input type="radio" name="type" value="etudiant" {{ old('type') == 'etudiant' ? 'checked' : '' }} class="profile-type-input">
                                        <div class="profile-type-card">
                                            <div class="profile-type-icon etudiant">
                                                <i class="bi bi-mortarboard"></i>
                                            </div>
                                            <div class="profile-type-text">
                                                <h4>Étudiant</h4>
                                                <p>Accédez aux formations et au contenu des cours</p>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="profile-type-option">
                                        <input type="radio" name="type" value="enseignant" {{ old('type') == 'enseignant' ? 'checked' : '' }} class="profile-type-input">
                                        <div class="profile-type-card">
                                            <div class="profile-type-icon enseignant">
                                                <i class="bi bi-briefcase"></i>
                                            </div>
                                            <div class="profile-type-text">
                                                <h4>Enseignant</h4>
                                                <p>Créez et gérez des cours et des ressources</p>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="profile-type-option">
                                        <input type="radio" name="type" value="admin" {{ old('type') == 'admin' ? 'checked' : '' }} class="profile-type-input">
                                        <div class="profile-type-card">
                                            <div class="profile-type-icon admin">
                                                <i class="bi bi-shield-lock"></i>
                                            </div>
                                            <div class="profile-type-text">
                                                <h4>Administrateur</h4>
                                                <p>Gérez l'ensemble de la plateforme et des utilisateurs</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @error('type')
                                    <div class="error-message">
                                        <i class="bi bi-exclamation-circle"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group" id="formation-field" style="display: none;">
                            <label for="formation_id" class="form-label">Formation (obligatoire pour les étudiants)</label>
                            <div class="select-container">
                                <div class="select-wrapper">
                                    <i class="bi bi-book"></i>
                                    <select id="formation_id" name="formation_id"
                                        class="form-select @error('formation_id') is-invalid @enderror">
                                        <option value="">-- Sélectionnez une formation --</option>
                                        @foreach ($formations as $formation)
                                            <option value="{{ $formation->id }}" {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                                                {{ $formation->intitule }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="bi bi-chevron-down select-arrow"></i>
                                </div>
                                @error('formation_id')
                                    <div class="error-message">
                                        <i class="bi bi-exclamation-circle"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="password-strength-container">
                        <div class="password-strength">
                            <div class="strength-label">Force du mot de passe:</div>
                            <div class="strength-meter">
                                <div class="strength-segment" data-strength="1"></div>
                                <div class="strength-segment" data-strength="2"></div>
                                <div class="strength-segment" data-strength="3"></div>
                                <div class="strength-segment" data-strength="4"></div>
                            </div>
                            <div class="strength-text">Pas encore saisi</div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('login') }}" class="btn-cancel">
                            <i class="bi bi-arrow-left"></i>
                            <span>Retour à la connexion</span>
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-check-lg"></i>
                            <span>Créer mon compte</span>
                        </button>
                    </div>

                    <div class="terms-container">
                        <p class="terms-text">
                            En créant un compte, vous acceptez nos
                            <a href="#" class="terms-link">conditions d'utilisation</a>
                            et notre <a href="#" class="terms-link">politique de confidentialité</a>.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('mdp');
            const strengthMeter = document.querySelector('.strength-meter');
            const strengthText = document.querySelector('.strength-text');
            const segments = document.querySelectorAll('.strength-segment');

            passwordInput.addEventListener('input', function () {
                const val = passwordInput.value;
                let strength = 0;

                if (val.length >= 8) strength += 1;
                if (val.match(/[A-Z]/)) strength += 1;
                if (val.match(/[0-9]/)) strength += 1;
                if (val.match(/[^A-Za-z0-9]/)) strength += 1;

                segments.forEach(segment => {
                    const segmentStrength = parseInt(segment.getAttribute('data-strength'));
                    if (segmentStrength <= strength) {
                        segment.classList.add('active');
                    } else {
                        segment.classList.remove('active');
                    }
                });

                if (val.length === 0) {
                    strengthText.textContent = 'Pas encore saisi';
                    strengthText.className = 'strength-text';
                } else if (strength < 2) {
                    strengthText.textContent = 'Faible';
                    strengthText.className = 'strength-text weak';
                } else if (strength < 4) {
                    strengthText.textContent = 'Moyen';
                    strengthText.className = 'strength-text medium';
                } else {
                    strengthText.textContent = 'Fort';
                    strengthText.className = 'strength-text strong';
                }
            });

            function toggleFormationField() {
                const studentRadio = document.querySelector('input[name="type"][value="etudiant"]');
                const formationField = document.getElementById('formation-field');

                if (studentRadio && studentRadio.checked) {
                    formationField.style.display = 'block';
                } else {
                    formationField.style.display = 'none';
                }
            }

            const typeRadios = document.querySelectorAll('input[name="type"]');
            typeRadios.forEach(radio => {
                radio.addEventListener('change', toggleFormationField);
            });

            toggleFormationField();

            const closeButtons = document.querySelectorAll('.alert-close');
            closeButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const alert = this.closest('.alert');
                    alert.classList.add('fade-out');
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                });
            });
        });
    </script>
@endsection