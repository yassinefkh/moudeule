@extends('layouts.modele')
@section('title', 'Liste des cours')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/cours-index.css') }}">

    <div class="courses-container">
        <div class="title-section">
            <h1><i class="bi bi-journal-code me-2"></i>Liste des cours</h1>
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



        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('cours.create') }}" class="btn btn-add-course">
                <i class="bi bi-plus-circle me-1"></i> Ajouter un cours
            </a>
        </div>

        @if ($cours->isEmpty())
            <div class="custom-alert custom-alert-info justify-content-center text-center">
                <i class="bi bi-info-circle-fill me-2"></i>
                Aucun cours n’est disponible actuellement.
            </div>

        @else
            @foreach ($cours as $course)
                <div class="course-card">
                    <div class="course-title">{{ $course->intitule }}</div>
                    <div class="course-details mt-2">
                        <span><i class="bi bi-person"></i> {{ $course->user->prenom }} {{ $course->user->nom }}</span>
                        <span><i class="bi bi-mortarboard"></i> {{ $course->formation->intitule }}</span>
                        <span><i class="bi bi-hash"></i> ID {{ $course->id }}</span>
                    </div>

                    <div class="card-actions">
                        <a href="{{ route('cours.edit', $course->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil-square me-1"></i> Modifier
                        </a>
                        <form action="{{ route('cours.destroy', $course->id) }}" method="POST"
                            onsubmit="return confirm('Supprimer ce cours ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash3 me-1"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            <div class="d-flex justify-content-center pagination-custom">
                {{ $cours->links() }}
            </div>
        @endif
    </div>
@endsection