@extends('layouts.modele')
@section('title', 'Formations')

@section('content')

    <link rel="stylesheet" href="{{ asset('/css/admin-formations-index.css') }}">


    <div class="page-background admin-background"></div>

    <div class="admin-container formations-container">

        <div class="admin-page-header" data-aos="fade-down">
            <div class="admin-header-title">
                <h1 class="admin-title"><i class="bi bi-mortarboard-fill me-2"></i>Formations</h1>
                <p class="admin-subtitle">Gérez les parcours académiques disponibles</p>
            </div>
            <a href="{{ route('admin.formations.create') }}" class="btn btn-primary btn-glow">
                <span class="btn-text">Nouvelle Formation</span>
                <i class="bi bi-plus-lg"></i>
            </a>
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


        @if ($formations->isEmpty())

            <div class="empty-state" data-aos="fade-up">
                <div class="empty-state-icon">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <h2 class="empty-state-title">Aucune formation</h2>
                <p class="empty-state-message">Vous n'avez pas encore créé de formations.</p>
                <a href="{{ route('admin.formations.create') }}" class="btn btn-primary btn-glow mt-3">
                    <span class="btn-text">Créer votre première formation</span>
                    <i class="bi bi-plus-lg"></i>
                </a>
            </div>
        @else

            <div class="formations-grid" data-aos="fade-up">
                @foreach ($formations as $formation)
                    <div class="formation-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <div class="formation-card-content">
                            <div class="formation-card-header">
                                <h3 class="formation-title">{{ $formation->intitule }}</h3>
                                <div class="formation-badge">ID #{{ $formation->id }}</div>
                            </div>

                            <div class="formation-info">
                                <div class="info-item">
                                    <i class="bi bi-people"></i>

                                    <span>
                                        @if(isset($formation->etudiants_count))
                                            {{ $formation->etudiants_count }}
                                        @elseif(is_object($formation->etudiants))
                                            {{ $formation->etudiants->count() }}
                                        @else
                                            0
                                        @endif
                                        étudiants
                                    </span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-book"></i>

                                    <span>
                                        @if(isset($formation->cours_count))
                                            {{ $formation->cours_count }}
                                        @elseif(is_object($formation->cours))
                                            {{ $formation->cours->count() }}
                                        @else
                                            0
                                        @endif
                                        cours
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="formation-card-actions">
                            <a href="{{ route('admin.formations.edit', $formation->id) }}" class="btn btn-edit-formation">
                                <i class="bi bi-pencil-square"></i>
                                <span>Modifier</span>
                            </a>
                            <a href="{{ route('admin.formations.confirm-destroy', $formation->id) }}"
                                class="btn btn-delete-formation">
                                <i class="bi bi-trash"></i>
                                <span>Supprimer</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>


            @if (method_exists($formations, 'links') && $formations->hasPages())
                <div class="pagination-container mt-4">
                    {{ $formations->links() }}
                </div>
            @endif
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });

            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('mousemove', e => {
                    const rect = button.getBoundingClientRect();
                    const x = ((e.clientX - rect.left) / button.clientWidth) * 100;
                    const y = ((e.clientY - rect.top) / button.clientHeight) * 100;
                    button.style.setProperty('--mouse-x', `${x}%`);
                    button.style.setProperty('--mouse-y', `${y}%`);
                });
            });
        });
    </script>
@endsection