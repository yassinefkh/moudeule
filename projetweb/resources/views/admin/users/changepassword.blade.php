@extends('layouts.modele')
@section('title', 'Changer le mot de passe')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/admin-users-changepassword.css') }}">

    <div class="page-background admin-background"></div>

    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-content">
                <h1 class="admin-title">
                    <i class="bi bi-key"></i>
                    <span>Modification du mot de passe</span>
                </h1>
                <p class="admin-subtitle">Définir un nouveau mot de passe pour {{ $user->prenom }} {{ $user->nom }}</p>
            </div>
        </div>

        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Gestion des utilisateurs</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Modifier le mot de passe</li>
                </ol>
            </nav>
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

        <div class="password-card">
            <div class="user-profile">
                <div class="user-avatar {{ $user->type ?? 'admin' }}">
                    {{ substr($user->prenom ?? '', 0, 1) }}{{ substr($user->nom ?? '', 0, 1) }}
                </div>
                <div class="user-details">
                    <h2 class="user-name">{{ $user->prenom }} {{ $user->nom }}</h2>
                    <div class="user-info">
                        <span class="user-login">{{ $user->login }}</span>
                        <span class="user-badge 
                                @if ($user->type === null) badge-pending
                                @elseif ($user->type === 'etudiant') badge-etudiant
                                @elseif ($user->type === 'enseignant') badge-enseignant
                                @elseif ($user->type === 'admin') badge-admin
                                @endif">
                            @if ($user->type === null)
                                En attente
                            @elseif ($user->type === 'etudiant')
                                Étudiant
                            @elseif ($user->type === 'enseignant')
                                Enseignant
                            @elseif ($user->type === 'admin')
                                Administrateur
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="password-form-container">
                <form method="POST" action="{{ route('admin.users.changepassword', $user->id) }}" class="password-form">
                    @csrf
                    @method('POST')

                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="bi bi-shield-lock"></i>
                            <span>Réinitialisation du mot de passe</span>
                        </h3>
                        <p class="section-description">Définissez un nouveau mot de passe sécurisé. Le mot de passe doit
                            contenir au moins 8 caractères.</p>

                        <div class="form-group">
                            <label for="new_password" class="form-label">Nouveau mot de passe</label>
                            <div class="password-input-container">
                                <div class="password-input-wrapper">
                                    <i class="bi bi-lock"></i>
                                    <input type="password" id="new_password" name="new_password"
                                        class="form-input @error('new_password') is-invalid @enderror"
                                        placeholder="Saisissez un nouveau mot de passe" autocomplete="new-password">

                                </div>
                                @error('new_password')
                                    <div class="error-message">
                                        <i class="bi bi-exclamation-circle"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="new_password_confirmation" class="form-label">Confirmation du mot de passe</label>
                            <div class="password-input-container">
                                <div class="password-input-wrapper">
                                    <i class="bi bi-lock-fill"></i>
                                    <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                        class="form-input @error('new_password_confirmation') is-invalid @enderror"
                                        placeholder="Confirmez le nouveau mot de passe" autocomplete="new-password">

                                </div>
                                @error('new_password_confirmation')
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
                        <a href="{{ route('admin.users.index') }}" class="btn-cancel">
                            <i class="bi bi-arrow-left"></i>
                            <span>Retour</span>
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-check-lg"></i>
                            <span>Modifier le mot de passe</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButtons = document.querySelectorAll('.password-toggle');

            toggleButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const input = this.previousElementSibling;
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('bi-eye-slash', 'bi-eye');
                    } else {
                        input.type = 'password';
                        icon.classList.replace('bi-eye', 'bi-eye-slash');
                    }
                });
            });

            
            const passwordInput = document.getElementById('new_password');
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