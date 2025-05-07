@extends('layouts.modele')
@section('title', 'Saisie des notes')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/notes-create.css') }}">

<div class="page-background controls-background"></div>

<div class="controls-dashboard">
    <div class="container">
        <div class="dashboard-header" data-aos="fade-down">
            <div class="course-info">
                <h1 class="dashboard-title">Saisie des notes</h1>
                <p class="course-name">{{ $controle->titre }} - {{ $course->intitule }}</p>
            </div>
        </div>
        
        @if (session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
        
        @if (session('error'))
            <x-alert type="danger" :message="session('error')" />
        @endif

        <div class="form-container" data-aos="fade-up">
            @if($students->isEmpty())
                <div class="empty-state">
                    <div class="empty-illustration">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="empty-title">Aucun étudiant inscrit</h3>
                    <p class="empty-description">Il n'y a aucun étudiant inscrit à ce cours pour le moment.</p>
                    
                    <a href="{{ route('controles.show', [$course->id, $controle->id]) }}" class="btn-back-empty">
                        <i class="bi bi-arrow-left"></i>
                        <span>Retour au contrôle</span>
                    </a>
                </div>
            @else
                <form action="{{ route('notes.store', [$course->id, $controle->id]) }}" method="POST" class="grades-form">
                    @csrf
                    
                    <div class="form-header">
                        <div class="form-info">
                            <div class="info-item">
                                <span class="label">Date du contrôle</span>
                                <span class="value">{{ \Carbon\Carbon::parse($controle->date_controle)->format('d/m/Y') }}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Coefficient</span>
                                <span class="value">{{ $controle->coefficient }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grades-table-wrapper">
                        <table class="grades-table input-table">
                            <thead>
                                <tr>
                                    <th>Étudiant</th>
                                    <th width="120">Note /20</th>
                                    <th>Commentaire (optionnel)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    @php 
                                        $existingNote = $existingNotes->get($student->id);
                                        $noteValue = old('notes.'.$student->id, $existingNote ? $existingNote->note : '');
                                        $commentValue = old('commentaires.'.$student->id, $existingNote ? $existingNote->commentaire : '');
                                    @endphp
                                    <tr>
                                        <td class="student-cell">
                                            <div class="student-avatar">
                                                {{ strtoupper(substr($student->prenom, 0, 1) . substr($student->nom, 0, 1)) }}
                                            </div>
                                            <div class="student-info">
                                                <div class="student-name">{{ $student->prenom }} {{ $student->nom }}</div>
                                            </div>
                                        </td>
                                        <td class="grade-input-cell">
                                            <input type="number" name="notes[{{ $student->id }}]" min="0" max="20" step="0.01" class="form-control grade-input @error('notes.'.$student->id) is-invalid @enderror" value="{{ $noteValue }}">
                                            @error('notes.'.$student->id)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="comment-input-cell">
                                            <input type="text" name="commentaires[{{ $student->id }}]" class="form-control comment-input @error('commentaires.'.$student->id) is-invalid @enderror" value="{{ $commentValue }}" placeholder="Commentaire optionnel">
                                            @error('commentaires.'.$student->id)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="form-actions">
                        <a href="{{ route('controles.show', [$course->id, $controle->id]) }}" class="btn-cancel">
                            <i class="bi bi-x-lg"></i>
                            <span>Annuler</span>
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-check-lg"></i>
                            <span>Enregistrer les notes</span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
        
        <div class="actions-footer" data-aos="fade-up">
            <div class="input-tips">
                <i class="bi bi-info-circle"></i>
                <div>
                    <p class="tip-title">Astuces pour la saisie</p>
                    <p class="tip-description">Utilisez la touche Tab pour passer d'un champ à l'autre. Laissez le champ vide si l'étudiant n'a pas de note.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });
    });
</script>
@endsection