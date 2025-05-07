@extends('layouts.modele')
@section('title', 'Créer une séance')

@section('content')

    <link rel="stylesheet" href="{{ asset('/css/planning-create.css') }}">

    <div class="page-background session-background"></div>

    <div class="session-container">
        <div class="session-header">
            <h1 class="session-title">
                <i class="bi bi-calendar-plus"></i>
                <span>Créer une nouvelle séance</span>
            </h1>
            <p class="session-subtitle">Planifiez un nouveau cours dans l'emploi du temps</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger fade-in">
                <div class="alert-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="alert-content">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button class="alert-close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endif

        <div class="form-card">
            <form method="POST" action="{{ route('planning.store') }}" id="sessionForm">
                @csrf

                <div class="form-section">
                    <div class="form-section-header">
                        <div class="section-icon">
                            <i class="bi bi-book-fill"></i>
                        </div>
                        <div class="section-title">Détails du cours</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="course" class="form-label">Sélectionnez un cours</label>
                            <div class="input-wrapper">
                                <i class="bi bi-journal-richtext"></i>
                                <select class="form-select @error('course_id') is-invalid @enderror" id="course"
                                    name="course_id">
                                    <option value="" disabled selected>Choisir un cours</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->intitule }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-header">
                        <div class="section-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="section-title">Planification horaire</div>
                    </div>

                    <div class="form-row date-time-row">
                        <div class="form-group">
                            <label for="date_debut" class="form-label">Date et heure de début</label>
                            <div class="input-wrapper">
                                <i class="bi bi-calendar-event"></i>
                                <input type="datetime-local" class="form-control @error('date_debut') is-invalid @enderror"
                                    id="date_debut" name="date_debut" value="{{ old('date_debut') }}" required
                                    onchange="updateEndDate(this)">
                            </div>
                            @error('date_debut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="date_fin" class="form-label">Date et heure de fin</label>
                            <div class="input-wrapper">
                                <i class="bi bi-calendar-check"></i>
                                <input type="datetime-local" class="form-control @error('date_fin') is-invalid @enderror"
                                    id="date_fin" name="date_fin" value="{{ old('date_fin') }}" required>
                            </div>
                            @error('date_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group duration-display">
                            <div class="duration-label">Durée prévue:</div>
                            <div class="duration-value" id="sessionDuration">--:--</div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('planning.index') }}" class="btn-cancel">
                        <i class="bi bi-x-circle"></i>
                        <span>Annuler</span>
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-circle"></i>
                        <span>Créer la séance</span>
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

            updateDuration();


            document.getElementById('date_debut').addEventListener('change', function () {
                updateEndDate(this);
                updateDuration();
            });

            document.getElementById('date_fin').addEventListener('change', function () {
                updateDuration();
            });


            const closeButtons = document.querySelectorAll('.alert-close');
            closeButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const alert = this.closest('.alert');
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                });
            });
        });

        function updateEndDate(dateInput) {
            const endDateInput = document.getElementById('date_fin');
            if (!dateInput.value) return;

            endDateInput.min = dateInput.value;


            if (!endDateInput.value || new Date(endDateInput.value) < new Date(dateInput.value)) {

                const startDate = new Date(dateInput.value);
                const endDate = new Date(startDate.getTime() + 60 * 60000);


                const year = endDate.getFullYear();
                const month = String(endDate.getMonth() + 1).padStart(2, '0');
                const day = String(endDate.getDate()).padStart(2, '0');
                const hours = String(endDate.getHours()).padStart(2, '0');
                const minutes = String(endDate.getMinutes()).padStart(2, '0');

                endDateInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
            }

            updateDuration();
        }

        function updateDuration() {
            const startDateInput = document.getElementById('date_debut');
            const endDateInput = document.getElementById('date_fin');
            const durationDisplay = document.getElementById('sessionDuration');

            if (!startDateInput.value || !endDateInput.value) {
                durationDisplay.textContent = '--:--';
                return;
            }

            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);


            if (endDate <= startDate) {
                durationDisplay.textContent = 'Date de fin invalide';
                durationDisplay.style.color = '#ef4444';
                return;
            }


            const durationMs = endDate - startDate;
            const durationMinutes = Math.floor(durationMs / 60000);
            const hours = Math.floor(durationMinutes / 60);
            const minutes = durationMinutes % 60;

            durationDisplay.style.color = '#6366f1';

            if (hours > 0) {
                durationDisplay.textContent = `${hours}h ${minutes}min`;
            } else {
                durationDisplay.textContent = `${minutes} minutes`;
            }
        }
    </script>
@endsection