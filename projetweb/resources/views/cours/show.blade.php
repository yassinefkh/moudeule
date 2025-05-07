@extends('layouts.modele')

@section('title', 'Cours : ' . $course->intitule)

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/cours-show.css') }}">

    <section class="course-page">
        <div class="course-container">

            <div class="course-header">
                <div class="header-content">
                    <h1 class="course-title">{{ $course->intitule }}</h1>
                    <p class="course-subtitle">Consultez les ressources, documents et annonces liés à ce cours.</p>

                    <div class="header-meta">
                        <div class="meta-badge">
                            <i class="bi bi-mortarboard-fill"></i>
                            {{ $course->formation->intitule }}
                        </div>
                        <div class="meta-badge">
                            <i class="bi bi-person-vcard-fill"></i>
                            {{ $course->user->prenom }} {{ $course->user->nom }}
                        </div>
                    </div>

                    <div class="id-badge">
                        <i class="bi bi-hash"></i>
                        ID : {{ $course->id }}
                    </div>
                </div>
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


            @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                <div class="action-buttons">
                    <a href="{{ route('annonces.index', $course->id) }}" class="action-button announcement-button">
                        <i class="bi bi-megaphone-fill me-2"></i> Publier une annonce
                    </a>
                    <button type="button" class="action-button document-button" data-bs-toggle="modal"
                        data-bs-target="#addSectionModal">
                        <i class="bi bi-folder-plus me-2"></i> Ajouter une section
                    </button>
                    <a href="{{ route('controles.index', $course->id) }}" class="action-button grades-button">
                        <i class="bi bi-clipboard-check me-2"></i> Contrôles et notes
                    </a>
                    <a href="{{ route('controles.create', $course->id) }}" class="action-button create-test-button">
                        <i class="bi bi-plus-circle me-2"></i> Créer un contrôle
                    </a>
                </div>


                <div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content bg-dark text-light">
                            <div class="modal-header border-secondary">
                                <h5 class="modal-title" id="addSectionModalLabel">
                                    <i class="bi bi-folder-plus text-primary me-2"></i>Nouvelle section
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="{{ route('sections.store') }}" method="POST" id="sectionForm">
                                @csrf
                                <input type="hidden" name="cours_id" value="{{ $course->id }}">
                                <div class="modal-body">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="titre" id="titre"
                                            class="form-control bg-dark text-light border-secondary"
                                            placeholder="Titre de la section" required>
                                        <label for="titre">Titre de la section</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="number" name="ordre" id="ordre"
                                            class="form-control bg-dark text-light border-secondary" min="1"
                                            value="{{ $course->sections->count() + 1 }}" placeholder="Ordre d'affichage">
                                        <label for="ordre">Ordre d'affichage</label>
                                    </div>
                                </div>
                                <div class="modal-footer border-secondary">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i> Annuler
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i> Créer la section
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif


            @foreach ($course->sections->sortBy('ordre') as $section)
                <div class="content-section">
                    <div class="section-header">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="d-flex align-items-center">
                                <div class="section-icon">
                                    <i class="bi bi-collection"></i>
                                </div>
                                <h2 class="section-title">{{ $section->titre }}</h2>
                            </div>

                            @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                                <div class="section-actions d-flex gap-2">
                                    <button type="button" class="btn-download" data-bs-toggle="modal"
                                        data-bs-target="#addDocumentModal-{{ $section->id }}">
                                        <i class="bi bi-file-earmark-plus me-1"></i> Ajouter
                                    </button>

                                    <button type="button" class="btn-edit" data-bs-toggle="modal"
                                        data-bs-target="#editSectionModal-{{ $section->id }}">
                                        <i class="bi bi-pencil-square me-1"></i> Modifier
                                    </button>

                                    <form action="{{ route('sections.destroy', $section->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette section et tous ses documents ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">
                                            <i class="bi bi-trash-fill me-1"></i> Supprimer
                                        </button>
                                    </form>
                                </div>


                                <div class="modal fade" id="addDocumentModal-{{ $section->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-dark text-light">
                                            <form action="{{ route('documents.store', ['cours' => $course->id]) }}" method="POST"
                                                enctype="multipart/form-data" id="documentForm-{{ $section->id }}">
                                                @csrf
                                                <div class="modal-header border-secondary">
                                                    <h5 class="modal-title">
                                                        <i class="bi bi-file-earmark-plus text-primary me-2"></i>
                                                        Ajouter un document à "{{ $section->titre }}"
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <input type="hidden" name="section_id" value="{{ $section->id }}">

                                                    <div class="form-floating mb-4">
                                                        <input type="text" name="titre" id="titre-{{ $section->id }}"
                                                            class="form-control bg-dark text-light border-secondary"
                                                            placeholder="Titre du document" required>
                                                        <label for="titre-{{ $section->id }}">Titre du document</label>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label for="fichier-{{ $section->id }}" class="form-label d-block mb-2">
                                                            <i class="bi bi-upload me-1"></i> Sélectionner un fichier
                                                        </label>
                                                        <div class="file-upload-wrapper">
                                                            <input type="file" name="fichier" id="fichier-{{ $section->id }}"
                                                                class="form-control bg-dark text-light border-secondary file-upload-input"
                                                                required>
                                                            <div class="file-upload-preview mt-2 d-none">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="bi bi-file-earmark me-2 text-primary"></i>
                                                                    <span class="selected-filename"></span>
                                                                    <span class="selected-filesize ms-2 text-muted"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted d-block mt-2">
                                                            <i class="bi bi-info-circle me-1"></i>
                                                            Taille max : 50MB. Formats supportés : PDF, DOC, DOCX, PPT, PPTX, XLS,
                                                            XLSX, ZIP, RAR, JPG, PNG
                                                        </small>
                                                    </div>
                                                </div>

                                                <div class="modal-footer border-secondary">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                        <i class="bi bi-x-circle me-1"></i> Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bi bi-upload me-1"></i> Télécharger
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>


                                <div class="modal fade" id="editSectionModal-{{ $section->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-dark text-light">
                                            <div class="modal-header border-secondary">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-pencil-square text-primary me-2"></i>
                                                    Modifier la section
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('sections.update', $section->id) }}" method="POST"
                                                id="editSectionForm-{{ $section->id }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" name="titre" id="edit-titre-{{ $section->id }}"
                                                            class="form-control bg-dark text-light border-secondary"
                                                            value="{{ $section->titre }}" placeholder="Titre de la section"
                                                            required>
                                                        <label for="edit-titre-{{ $section->id }}">Titre de la section</label>
                                                    </div>
                                                    <div class="form-floating mb-3">
                                                        <input type="number" name="ordre" id="edit-ordre-{{ $section->id }}"
                                                            class="form-control bg-dark text-light border-secondary"
                                                            value="{{ $section->ordre }}" min="1" placeholder="Ordre d'affichage">
                                                        <label for="edit-ordre-{{ $section->id }}">Ordre d'affichage</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-secondary">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                        <i class="bi bi-x-circle me-1"></i> Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bi bi-check-circle me-1"></i> Enregistrer
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($section->description)
                        <div class="section-description">
                            {{ $section->description }}
                        </div>
                    @endif


                    <div class="section-content">
                        @if($section->documents->count())
                                <div class="document-list">
                                    @foreach($section->documents as $doc)
                                                @php
                                                    $extension = pathinfo($doc->fichier, PATHINFO_EXTENSION);
                                                    $iconClass = 'pdf-icon';
                                                    $icon = 'bi-file-earmark-pdf';

                                                    if (in_array($extension, ['doc', 'docx'])) {
                                                        $iconClass = 'doc-icon';
                                                        $icon = 'bi-file-earmark-word';
                                                    } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                                        $iconClass = 'xls-icon';
                                                        $icon = 'bi-file-earmark-excel';
                                                    } elseif (in_array($extension, ['ppt', 'pptx'])) {
                                                        $iconClass = 'ppt-icon';
                                                        $icon = 'bi-file-earmark-ppt';
                                                    } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                        $iconClass = 'img-icon';
                                                        $icon = 'bi-file-earmark-image';
                                                    } elseif (in_array($extension, ['zip', 'rar'])) {
                                                        $iconClass = 'zip-icon';
                                                        $icon = 'bi-file-earmark-zip';
                                                    } else {
                                                        $icon = 'bi-file-earmark-text';
                                                    }
                                                @endphp

                                                <div class="document-item hover-effect">
                                                    <div class="document-info">
                                                        <div class="file-icon {{ $iconClass }}">
                                                            <i class="bi {{ $icon }}"></i>
                                                        </div>
                                                        <div class="file-info">
                                                            <h3 class="file-name">{{ $doc->titre }}</h3>
                                                            <span class="file-details">
                                                                <span class="file-type">{{ strtoupper($extension) }}</span>
                                                                @if($doc->created_at)
                                                                    <span class="file-date">Ajouté le {{ $doc->created_at->format('d/m/Y') }}</span>
                                                                @endif
                                                                @if($doc->description)
                                                                    <span class="file-description"
                                                                        title="{{ $doc->description }}">{{ \Illuminate\Support\Str::limit($doc->description, 50) }}</span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="document-actions">
                                                        <a href="{{ route('documents.download', $doc->id) }}" class="btn-download">
                                                            <i class="bi bi-download"></i> Télécharger
                                                        </a>
                                                        @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                                                            <form method="POST" action="{{ route('documents.destroy', [$course->id, $doc->id]) }}"
                                                                class="d-inline"
                                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn-delete">
                                                                    <i class="bi bi-trash-fill"></i> Supprimer
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                    @endforeach
                                </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <p class="empty-text">Aucun document n'a encore été ajouté à cette section.</p>
                                @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addDocumentModal-{{ $section->id }}">
                                        <i class="bi bi-file-earmark-plus"></i> Ajouter un document
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach


            @if($course->sections->count() == 0)
                <div class="content-section">
                    <div class="empty-state">
                        <div class="empty-icon pulse-animation">
                            <i class="bi bi-collection"></i>
                        </div>
                        <p class="empty-text">
                            @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                                Aucune section n'a encore été créée. Créez des sections pour organiser vos documents de cours.
                            @else
                                Aucune section n'a encore été créée par l'enseignant.
                            @endif
                        </p>
                        @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                            <button type="button" class="btn btn-primary pulse-button" data-bs-toggle="modal"
                                data-bs-target="#addSectionModal">
                                <i class="bi bi-folder-plus me-2"></i> Ajouter une première section
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <div class="content-section">
                <div class="section-header">
                    <div class="d-flex align-items-center">
                        <div class="section-icon icon-grades">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <h2 class="section-title">Contrôles et Évaluations</h2>
                    </div>
                    @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                        <div class="section-actions">
                           
                        </div>
                    @endif
                </div>
                <div class="section-content">
                    @php
                        $controles = isset($course->controles) ? $course->controles->sortByDesc('date_controle') : collect([]);
                    @endphp

                    @if($controles->count())
                        <div class="controls-list">
                            @foreach($controles->take(3) as $controle)
                                <div class="control-item hover-effect">
                                    <div class="control-main">
                                        <div class="control-icon">
                                            <i class="bi bi-journal-check"></i>
                                        </div>
                                        <div class="control-info">
                                            <h3 class="control-title">{{ $controle->titre }}</h3>
                                            <div class="control-meta">
                                                <span class="control-date">
                                                    <i class="bi bi-calendar-event"></i>
                                                    {{ \Carbon\Carbon::parse($controle->date_controle)->format('d/m/Y') }}
                                                </span>
                                                <span class="control-coef">
                                                    <i class="bi bi-bar-chart"></i>
                                                    Coefficient: {{ $controle->coefficient }}
                                                </span>
                                            </div>
                                            @if($controle->description)
                                                <p class="control-description">
                                                    {{ \Illuminate\Support\Str::limit($controle->description, 100) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="control-details">
                                        @if(auth()->user()->type === 'etudiant')
                                            @php $userNote = $controle->getNoteForUser(auth()->id()); @endphp
                                            <div class="student-grade {{ $userNote && $userNote->note < 10 ? 'failing' : 'passing' }}">
                                                <span class="grade-value">{{ $userNote ? $userNote->note : 'N/A' }}</span>
                                                <span class="grade-label">/20</span>
                                            </div>
                                        @endif

                                        @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                                            <div class="control-stats">
                                                <div class="stat-item">
                                                    <span class="stat-label">Moyenne</span>
                                                    <span class="stat-value">{{ $controle->moyenne() ?? 'N/A' }}</span>
                                                </div>
                                                <div class="stat-item">
                                                    <span class="stat-label">Notes</span>
                                                    <span
                                                        class="stat-value">{{ $controle->notes->count() }}/{{ $course->students->count() }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                                            <div class="control-actions">
                                                <a href="{{ route('controles.show', [$course->id, $controle->id]) }}" class="btn-view">
                                                    <i class="bi bi-eye"></i> Détails
                                                </a>


                                                <a href="{{ route('notes.create', [$course->id, $controle->id]) }}" class="btn-grade">
                                                    <i class="bi bi-pencil-square"></i> Saisir notes
                                                </a>

                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            @if($controles->count() > 3)
                                <div class="view-all-container">
                                    <a href="{{ route('controles.index', $course->id) }}" class="btn-view-all">
                                        <i class="bi bi-list"></i> Voir tous les contrôles ({{ $controles->count() }})
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-journals"></i>
                            </div>
                            <p class="empty-text">
                                @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                                    Aucun contrôle n'a été créé pour ce cours. Créez des contrôles pour évaluer les étudiants.
                                @else
                                    Aucun contrôle n'est disponible pour ce cours pour le moment.
                                @endif
                            </p>

                            @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                                <a href="{{ route('controles.create', $course->id) }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Créer un contrôle
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="content-section">
                <div class="section-header">
                    <div class="d-flex align-items-center">
                        <div class="section-icon icon-announcement">
                            <i class="bi bi-megaphone-fill"></i>
                        </div>
                        <h2 class="section-title">Annonces du cours</h2>
                    </div>
                </div>
                <div class="section-content">
                    @if($course->annonces->count())
                                    <div class="announcement-list">
                                        @php
                                            $annonces = $course->annonces->sortByDesc('created_at');
                                            $visibleAnnonces = $annonces->take(3);
                                            $hiddenAnnonces = $annonces->slice(3);
                                        @endphp

                                        @foreach($visibleAnnonces as $annonce)
                                            <div class="announcement-item {{ $annonce->important ? 'important' : '' }}">
                                                <div class="announcement-header">
                                                    <div class="announcement-title-wrapper">
                                                        <div class="announcement-badge">
                                                            <i class="bi bi-bell-fill"></i>
                                                        </div>
                                                        <h3 class="announcement-title">{{ $annonce->titre }}</h3>
                                                    </div>
                                                    <div class="announcement-meta">
                                                        <div class="announcement-author">
                                                            <div class="author-avatar">
                                                                {{ substr($course->user->prenom, 0, 1) }}{{ substr($course->user->nom, 0, 1) }}
                                                            </div>
                                                            <span class="author-name">{{ $course->user->prenom }}
                                                                {{ $course->user->nom }}</span>
                                                        </div>
                                                        <div class="announcement-date">
                                                            <i class="bi bi-calendar3"></i>
                                                            {{ $annonce->created_at->format('d/m/Y') }}
                                                        </div>
                                                        <div class="announcement-time">
                                                            <i class="bi bi-clock"></i>
                                                            {{ $annonce->created_at->format('H:i') }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="announcement-content">
                                                    <p>{{ $annonce->contenu }}</p>
                                                </div>

                                                <div class="announcement-footer">
                                                    <div class="announcement-tags">
                                                        @if($annonce->important)
                                                            <span class="tag tag-important">Important</span>
                                                        @endif
                                                        @if(isset($annonce->type))
                                                            <span class="tag tag-{{ strtolower($annonce->type) }}">{{ $annonce->type }}</span>
                                                        @endif
                                                    </div>

                                                    @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                                                        <div class="announcement-actions">
                                                            <form method="POST"
                                                                action="{{ route('annonces.destroy', [$course->id, $annonce->id]) }}"
                                                                class="delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn-delete"
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce?')">
                                                                    <i class="bi bi-trash-fill"></i>
                                                                    <span>Supprimer</span>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach

                                        @foreach($hiddenAnnonces as $annonce)
                                            <div class="announcement-item {{ $annonce->important ? 'important' : '' }} hidden">

                                                <div class="announcement-header">
                                                    <div class="announcement-title-wrapper">
                                                        <div class="announcement-badge">
                                                            <i class="bi bi-bell-fill"></i>
                                                        </div>
                                                        <h3 class="announcement-title">{{ $annonce->titre }}</h3>
                                                    </div>
                                                    <div class="announcement-meta">
                                                        <div class="announcement-author">
                                                            <div class="author-avatar">
                                                                {{ substr($course->user->prenom, 0, 1) }}{{ substr($course->user->nom, 0, 1) }}
                                                            </div>
                                                            <span class="author-name">{{ $course->user->prenom }}
                                                                {{ $course->user->nom }}</span>
                                                        </div>
                                                        <div class="announcement-date">
                                                            <i class="bi bi-calendar3"></i>
                                                            {{ $annonce->created_at->format('d/m/Y') }}
                                                        </div>
                                                        <div class="announcement-time">
                                                            <i class="bi bi-clock"></i>
                                                            {{ $annonce->created_at->format('H:i') }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="announcement-content">
                                                    <p>{{ $annonce->contenu }}</p>
                                                </div>

                                                <div class="announcement-footer">
                                                    <div class="announcement-tags">
                                                        @if($annonce->important)
                                                            <span class="tag tag-important">Important</span>
                                                        @endif
                                                        @if(isset($annonce->type))
                                                            <span class="tag tag-{{ strtolower($annonce->type) }}">{{ $annonce->type }}</span>
                                                        @endif
                                                    </div>

                                                    @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                                                        <div class="announcement-actions">
                                                            <form method="POST"
                                                                action="{{ route('annonces.destroy', [$course->id, $annonce->id]) }}"
                                                                class="delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn-delete"
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce?')">
                                                                    <i class="bi bi-trash-fill"></i>
                                                                    <span>Supprimer</span>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($course->annonces->count() > 3)
                                        <div class="pagination-container">
                                            <button class="load-more-btn">
                                                <i class="bi bi-plus-circle me-1"></i>
                                                Voir plus d'annonces
                                            </button>
                                        </div>
                                    @endif
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-megaphone"></i>
                            </div>
                            <p class="empty-text">Aucune annonce n'a été publiée pour ce cours.</p>
                            @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                                <a href="{{ route('annonces.index', $course->id) }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i> Publier une annonce
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>


            <div class="content-section">
                <div class="section-header">
                    <div class="d-flex align-items-center">
                        <div class="section-icon icon-calendar">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <h2 class="section-title">Planning des séances</h2>
                    </div>
                </div>
                <div class="section-content">
                    @if($course->plannings->count())
                        <div class="planning-list">
                            @foreach($course->plannings->sortBy('date') as $planning)
                                @php $date = \Carbon\Carbon::parse($planning->date); @endphp
                                <div class="planning-item hover-effect">
                                    <div class="date-box">
                                        <div class="date-day">{{ $date->format('d') }}</div>
                                        <div class="date-month">{{ $date->format('M') }}</div>
                                    </div>
                                    <div class="date-info">
                                        <div class="date-weekday">{{ $date->translatedFormat('l d F Y') }}</div>
                                        <div class="date-time">
                                            <i class="bi bi-clock"></i>
                                            {{ \Carbon\Carbon::parse($planning->date_debut)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($planning->date_fin)->format('H:i') }}
                                        </div>
                                    </div>
                                    @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                                        <div class="planning-actions">
                                            <a href="{{ route('planning.edit', $planning->id) }}" class="btn-edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-calendar3"></i>
                            </div>
                            <p class="empty-text">Aucune séance n'est planifiée pour ce cours.</p>
                            @if(auth()->user()->type === 'enseignant' || auth()->user()->type === 'admin')
                                <a href="{{ route('planning.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i> Planifier une séance
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const loadMoreBtn = document.querySelector('.load-more-btn');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function () {
                    const hiddenAnnouncements = document.querySelectorAll('.announcement-item.hidden');
                    let count = 0;
                    hiddenAnnouncements.forEach(item => {
                        if (count < 3) {
                            item.classList.add('fade-in');
                            item.classList.remove('hidden');
                            count++;
                        }
                    });


                    if (document.querySelectorAll('.announcement-item.hidden').length === 0) {
                        loadMoreBtn.style.display = 'none';
                    }
                });
            }


            const announcements = document.querySelectorAll('.announcement-item');
            const now = new Date();

            announcements.forEach(item => {
                const dateEl = item.querySelector('.announcement-date');
                const timeEl = item.querySelector('.announcement-time');

                if (dateEl && timeEl) {
                    const dateText = dateEl.textContent.trim().replace(/[^\d/]/g, '');
                    const timeText = timeEl.textContent.trim().replace(/[^\d:]/g, '');

                    const [day, month, year] = dateText.split('/');
                    const [hours, minutes] = timeText.split(':');

                    const announcementDate = new Date(`${year}-${month}-${day}T${hours}:${minutes}:00`);
                    const diffTime = Math.abs(now - announcementDate);
                    const diffHours = diffTime / (1000 * 60 * 60);

                    if (diffHours < 24) {
                        item.classList.add('new');
                    }
                }
            });


            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function () {
                    const file = this.files[0];
                    if (!file) return;


                    const previewContainer = this.closest('.file-upload-wrapper').querySelector('.file-upload-preview');
                    const fileNameEl = previewContainer?.querySelector('.selected-filename');
                    const fileSizeEl = previewContainer?.querySelector('.selected-filesize');

                    if (previewContainer) {

                        const maxSizeMB = 50;
                        const maxSizeBytes = maxSizeMB * 1024 * 1024;


                        if (file.size > maxSizeBytes) {

                            previewContainer.classList.remove('d-none');
                            previewContainer.classList.add('bg-danger', 'bg-opacity-25');
                            if (fileNameEl) fileNameEl.textContent = 'Fichier trop volumineux';
                            if (fileSizeEl) fileSizeEl.textContent = `(${(file.size / (1024 * 1024)).toFixed(2)} MB / Max: ${maxSizeMB} MB)`;


                            setTimeout(() => {
                                this.value = '';
                            }, 100);
                        } else {

                            previewContainer.classList.remove('d-none', 'bg-danger', 'bg-opacity-25');
                            previewContainer.classList.add('bg-success', 'bg-opacity-10');
                            if (fileNameEl) fileNameEl.textContent = file.name;
                            if (fileSizeEl) fileSizeEl.textContent = `(${(file.size / 1024).toFixed(1)} KB)`;
                        }
                    }
                });
            });


            const alerts = document.querySelectorAll('.custom-alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    alert.style.transition = 'all 0.5s ease';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
@endsection