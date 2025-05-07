@extends('layouts.modele')
@section('title', 'Connexion')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/auth-login.css') }}">

    <div class="page-background auth-background"></div>

    <div class="auth-container">
        <div class="auth-header">
            <div class="auth-header-content">
                <h1 class="auth-title">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Connexion</span>
                </h1>
                <p class="auth-subtitle">Accédez à votre espace personnel pour gérer vos cours et activités</p>
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

        @if (session('success'))
            <div class="alert alert-success fade-in">
                <div class="alert-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="alert-content">
                    <p>{{ session('success') }}</p>
                </div>
                <button class="alert-close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning fade-in">
                <div class="alert-icon">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div class="alert-content">
                    <p>{{ session('warning') }}</p>
                </div>
                <button class="alert-close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        <div class="auth-card">
            <div class="auth-form-container">
                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="bi bi-shield-lock"></i>
                            <span>Identification</span>
                        </h3>
                        <p class="section-description">Entrez vos identifiants pour accéder à votre compte.</p>

                        <div class="form-group">
                            <label for="login" class="form-label">Identifiant</label>
                            <div class="input-container">
                                <div class="input-wrapper">

                                    <input type="text" id="login" name="login"
                                        class="form-input @error('login') is-invalid @enderror"
                                        placeholder="Entrez votre identifiant" value="{{ old('login') }}"
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
                                    <i class="bi bi-lock-fill"></i>
                                    <input type="password" id="mdp" name="mdp"
                                        class="form-input @error('mdp') is-invalid @enderror"
                                        placeholder="Entrez votre mot de passe" autocomplete="current-password">

                                </div>
                                @error('mdp')
                                    <div class="error-message">
                                        <i class="bi bi-exclamation-circle"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">

                        <button type="submit" class="btn-submit">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Se connecter</span>
                        </button>
                    </div>

                    <div class="form-links">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="form-link">
                                <i class="bi bi-key"></i>
                                <span>Mot de passe oublié ?</span>
                            </a>
                        @endif

                        <a href="{{ route('register') }}" class="form-link">
                            <i class="bi bi-person-plus"></i>
                            <span>Créer un compte</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            
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