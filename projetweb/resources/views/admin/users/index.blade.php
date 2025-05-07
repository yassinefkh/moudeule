@extends('layouts.modele')
@section('title', 'Gestion des utilisateurs')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/admin-users-index.css') }}">

    <div class="page-background admin-background"></div>

    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-header-content">
                <h1 class="admin-title">
                    <i class="bi bi-people"></i>
                    <span>Gestion des utilisateurs</span>
                    @if ($enAttente > 0)
                        <span class="pending-badge" data-count="{{ $enAttente }}">
                            {{ $enAttente }}
                        </span>
                    @endif
                </h1>
                <p class="admin-subtitle">Gérez les comptes d'utilisateurs, approuvez les demandes et définissez les rôles
                </p>
            </div>
        </div>

        @if(session('success'))
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

        @if(session('error'))
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


        <div class="stats-summary">
            <div class="stat-card admin">
                <div class="stat-header">
                    <div class="stat-title">Administrateurs</div>
                    <div class="stat-icon admin">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $users->where('type', 'admin')->count() }}</div>
                <div class="stat-change">Total des comptes admin</div>
            </div>
            <div class="stat-card enseignant">
                <div class="stat-header">
                    <div class="stat-title">Enseignants</div>
                    <div class="stat-icon enseignant">
                        <i class="bi bi-briefcase"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $users->where('type', 'enseignant')->count() }}</div>
                <div class="stat-change">Total des comptes enseignant</div>
            </div>
            <div class="stat-card etudiant">
                <div class="stat-header">
                    <div class="stat-title">Étudiants</div>
                    <div class="stat-icon etudiant">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $users->where('type', 'etudiant')->count() }}</div>
                <div class="stat-change">Total des comptes étudiant</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-header">
                    <div class="stat-title">En attente</div>
                    <div class="stat-icon pending">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $enAttente }}</div>
                <div class="stat-change">Comptes à approuver</div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="bi bi-filter-circle"></i>
                    <span>Filtrer les utilisateurs</span>
                </h2>
            </div>
            <div class="panel-body">
                <form method="GET" action="{{ route('admin.users.index') }}" class="filter-form">
                    <div class="filter-grid">
                        <div class="filter-group">
                            <label for="search" class="filter-label">Rechercher</label>
                            <div class="filter-input-wrapper">
                                <input type="text" name="search" id="search" class="filter-input"
                                    value="{{ request('search') }}" placeholder="Nom, prénom ou login...">
                            </div>
                        </div>

                        <div class="filter-group">
                            <label for="type" class="filter-label">Type d'utilisateur</label>
                            <div class="filter-input-wrapper">
                                <i class="bi bi-person-badge"></i>
                                <select name="type" id="type" class="filter-input">
                                    <option value="">Tous les types</option>
                                    <option value="etudiant" {{ request('type') == 'etudiant' ? 'selected' : '' }}>Étudiant
                                    </option>
                                    <option value="enseignant" {{ request('type') == 'enseignant' ? 'selected' : '' }}>
                                        Enseignant</option>
                                    <option value="admin" {{ request('type') == 'admin' ? 'selected' : '' }}>Administrateur
                                    </option>
                                    <option value="pending" {{ request('type') == 'pending' ? 'selected' : '' }}>En attente
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="btn btn-filter">
                                <i class="bi bi-funnel"></i>
                                <span>Appliquer les filtres</span>
                            </button>

                            <a href="{{ route('users.create') }}" class="btn btn-create">
                                <i class="bi bi-person-plus"></i>
                                <span>Nouvel utilisateur</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($enAttente > 0 && request('type') !== 'pending')
            <div class="pending-alert">
                <div class="pending-alert-icon">
                    <i class="bi bi-bell"></i>
                </div>
                <div class="pending-alert-content">
                    <h3>{{ $enAttente }} utilisateur(s) en attente d'approbation</h3>
                    <p>Des comptes utilisateurs sont en attente de validation. Veuillez les traiter.</p>
                </div>
                <div class="pending-alert-action">
                    <a href="{{ route('admin.users.index', ['type' => 'pending']) }}" class="btn btn-view-pending">
                        <i class="bi bi-eye"></i>
                        <span>Voir les demandes</span>
                    </a>
                </div>
            </div>
        @endif

        @if ($users->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-people-slash"></i>
                </div>
                <h2 class="empty-title">Aucun utilisateur trouvé</h2>
                <p class="empty-message">Aucun utilisateur ne correspond à vos critères de recherche.</p>
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>Réinitialiser les filtres</span>
                </a>
            </div>
        @else
            <div class="users-table-container">
                <div class="users-table-wrapper">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar {{ $user->type ?? 'pending' }}">
                                                {{ substr($user->prenom ?? '', 0, 1) }}{{ substr($user->nom ?? '', 0, 1) }}
                                            </div>
                                            <div class="user-detail">
                                                <div class="user-name">{{ $user->prenom }} {{ $user->nom }}</div>
                                                <div class="user-login">{{ $user->login }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-badge 
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
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->type === null)
                                            <span class="user-badge badge-pending">Validation requise</span>
                                        @else
                                            <span class="user-badge badge-admin">Actif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            @if ($user->type === null)
                                                <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-action btn-approve">
                                                        <i class="bi bi-check-circle"></i>
                                                        <span>Approuver</span>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.users.refuse', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-action btn-refuse">
                                                        <i class="bi bi-x-circle"></i>
                                                        <span>Refuser</span>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('admin.users.changepassword', $user) }}"
                                                    class="btn btn-action btn-password">
                                                    <i class="bi bi-key"></i>
                                                    <span>Mot de passe</span>
                                                </a>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                    onsubmit="return confirmDelete(this, '{{ $user->prenom }} {{ $user->nom }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-action btn-delete">
                                                        <i class="bi bi-trash"></i>
                                                        <span>Supprimer</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pagination-container">
                {{ $users->appends(request()->input())->links() }}
            </div>
        @endif
    </div>

    <script>
        function confirmDelete(form, username) {
            return confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${username}" ? Cette action est irréversible.`);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.classList.add('fade-out');
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
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