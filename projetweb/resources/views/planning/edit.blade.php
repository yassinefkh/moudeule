@extends('layouts.modele')
@section('title', 'Modifier séance')
@section('content')

    <link rel="stylesheet" href="{{ asset('/css/planning-edit.css') }}">

    <section class="edit-session-page">
        <div class="edit-session-container">
            <div class="form-card">
                <div class="form-header">
                    <h1 class="form-title">Modifier la séance de cours</h1>
                    <p class="form-subtitle">Ajustez les dates et heures pour cette séance</p>
                </div>

                <div class="form-body">
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

                    <form action="{{ route('planning.update', $session->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="date_debut" class="form-label">
                                <i class="bi bi-calendar-event"></i>
                                Date de début
                            </label>
                            <input type="datetime-local" class="form-control @error('date_debut') is-invalid @enderror"
                                id="date_debut" name="date_debut"
                                value="{{ old('date_debut', \Carbon\Carbon::parse($session->date_debut)->format('Y-m-d\TH:i:s')) }}"
                                required>

                            @error('date_debut')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror

                            <div class="datetime-info">
                                <i class="bi bi-info-circle"></i>
                                Date et heure actuelles:
                                {{ \Carbon\Carbon::parse($session->date_debut)->format('d/m/Y à H:i') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="date_fin" class="form-label">
                                <i class="bi bi-calendar-check"></i>
                                Date de fin
                            </label>
                            <input type="datetime-local" class="form-control @error('date_fin') is-invalid @enderror"
                                id="date_fin" name="date_fin"
                                value="{{ old('date_fin', \Carbon\Carbon::parse($session->date_fin)->format('Y-m-d\TH:i:s')) }}"
                                required>

                            @error('date_fin')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror

                            <div class="datetime-info">
                                <i class="bi bi-info-circle"></i>
                                Date et heure actuelles:
                                {{ \Carbon\Carbon::parse($session->date_fin)->format('d/m/Y à H:i') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="datetime-info">
                                <i class="bi bi-clock-history"></i>
                                Durée prévue:
                                <strong>
                                    {{ \Carbon\Carbon::parse($session->date_debut)->diffInHours(\Carbon\Carbon::parse($session->date_fin)) }}
                                    heure(s)
                                </strong>
                            </div>
                        </div>

                        <div class="actions-row">
                            <a href="{{ route('planning.index', $session->course_id) }}" class="btn btn-light">
                                <i class="bi bi-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection