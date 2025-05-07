@extends('layouts.modele')

@section('content')
    <div class="container">
        <h1 class="text-center">Créer une séance de cours</h1>
        <form action="{{ route('planning.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="intitule" class="form-label">Intitulé de la séance</label>
                <input type="text" name="intitule" id="intitule" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="date_debut" class="form-label">Date de début</label>
                <input type="datetime-local" name="date_debut" id="date_debut" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="date_fin" class="form-label">Date de fin</label <input type="datetime-local" name="date_fin"
                    id="date_fin" class="form-control" required>

            </div>

            <div class="mb-3">
                <label for="cours" class="form-label">Cours</label>
                <select name="cours" id="cours" class="form-control" required>
                    <option value="">Sélectionner un cours</option>
                    @foreach ($cours as $course)
                        <option value="{{ $course->id }}">{{ $course->intitule }}</option>
                    @endforeach
                </select>
            </div>

            @if (Auth::user()->type == 'admin')
                <div class="mb-3">
                    <label for="enseignant" class="form-label">Enseignant</label>
                    <select name="enseignant" id="enseignant" class="form-control" required>
                        <option value="">Sélectionner un enseignant</option>
                        @foreach ($enseignants as $enseignant)
                            <option value="{{ $enseignant->id }}">{{ $enseignant->prenom }} {{ $enseignant->nom }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Créer</button>
            </div>
        </form>
    </div>
@endsection