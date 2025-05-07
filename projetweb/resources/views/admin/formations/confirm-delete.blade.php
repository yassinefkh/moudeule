@extends('layouts.modele')
@section('title', 'Confirmer la suppression')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/formations-confirm-delete.css') }}">

    <div class="page-background admin-background"></div>

    <div class="admin-container">
        <div class="confirm-delete-card">
            <div class="confirm-header">
                <div class="confirm-icon danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h1 class="confirm-title">Attention : Action irréversible</h1>
            </div>

            <div class="confirm-body">
                <p class="confirm-message">
                    Vous êtes sur le point de supprimer la formation <strong>{{ $formation->intitule }}</strong>.
                </p>

                @if($formation->etudiants_count > 0)
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-diamond-fill me-2"></i>
                        <strong>Cette formation compte {{ $formation->etudiants_count }} étudiant(s) inscrit(s).</strong>
                        <p class="mt-2">Si vous supprimez cette formation, <strong>tous les comptes étudiants associés seront
                                définitivement supprimés</strong>. Cette action est irréversible.</p>
                    </div>

                    <div class="student-list-container mt-4">
                        <h3>Étudiants qui seront supprimés ({{ $formation->etudiants_count }})</h3>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Login</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($formation->etudiants as $etudiant)
                                        <tr>
                                            <td>{{ $etudiant->nom }}</td>
                                            <td>{{ $etudiant->prenom }}</td>
                                            <td>{{ $etudiant->login }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.formations.destroy', $formation->id) }}" method="POST" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="confirm" value="yes">

                    <div class="confirmation-actions">
                        <a href="{{ route('admin.formations.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i>
                            <span>Annuler</span>
                        </a>

                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i>
                            <span>Confirmer la suppression</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection