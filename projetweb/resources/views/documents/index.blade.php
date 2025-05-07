@extends('layouts.modele')

@section('title', 'Documents du cours')

@section('content')

    <link rel="stylesheet" href="{{ asset('/css/documents-index.css') }}">

    <div class="documents-container">
        <div class="page-header">
            <h1>Documents du cours : {{ $cours->intitule }}</h1>
            <p>Ajoutez ou gérez les fichiers associés à ce cours</p>
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

        <div class="section-card">
            <h2 class="section-title"><i class="fas fa-file-alt"></i> Documents existants</h2>

            @if($cours->documents->count())
                @foreach($cours->documents as $doc)
                    <div class="document-item">
                        <a href="{{ route('documents.download', $doc->id) }}" class="document-link">
                            <i class="fas fa-file-pdf"></i> {{ $doc->titre }}
                        </a>
                        <form method="POST" action="{{ route('documents.destroy', [$cours->id, $doc->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="delete-btn" type="submit">
                                <i class="fas fa-trash-alt"></i> Supprimer
                            </button>
                        </form>
                    </div>
                @endforeach
            @else
                <p class="empty-msg">
                    <i class="fas fa-info-circle"></i> Aucun document pour ce cours.
                </p>
            @endif
        </div>

        <div class="section-card">
            <h2 class="section-title"><i class="fas fa-upload"></i> Ajouter un nouveau document</h2>

            <form method="POST" action="{{ route('documents.store', $cours->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre du document</label>
                    <input type="text" name="titre" id="titre" class="form-control @error('titre') is-invalid @enderror"
                        value="{{ old('titre') }}" required>
                    @error('titre')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="fichier" class="form-label">Fichier (PDF, Word, PowerPoint, Excel, images, etc.)</label>
                    <input type="file" name="fichier" id="fichier"
                        class="form-control @error('fichier') is-invalid @enderror" required>
                    @error('fichier')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <small class="form-text text-muted">
                        Formats acceptés : PDF, Word, PowerPoint, Excel, archives (ZIP/RAR) et images. Taille maximale : 400
                        Mo.
                    </small>
                </div>

                @if(isset($cours->sections) && $cours->sections->count() > 0)
                    <div class="mb-3">
                        <label for="section_id" class="form-label">Section (optionnel)</label>
                        <select name="section_id" id="section_id"
                            class="form-control @error('section_id') is-invalid @enderror">
                            <option value="">-- Aucune section --</option>
                            @foreach($cours->sections as $section)
                                <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                    {{ $section->titre }}
                                </option>
                            @endforeach
                        </select>
                        @error('section_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                @endif

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Uploader
                </button>
            </form>
        </div>
    </div>


    <script>

        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('fichier');
            const maxFileSize = 400 * 1024 * 1024;

            fileInput.addEventListener('change', function () {
                if (this.files.length > 0) {
                    const fileSize = this.files[0].size;
                    if (fileSize > maxFileSize) {
                        alert('Le fichier est trop volumineux. La taille maximale autorisée est de 400 Mo.');
                        this.value = '';
                    }
                }
            });
        });
    </script>
@endsection