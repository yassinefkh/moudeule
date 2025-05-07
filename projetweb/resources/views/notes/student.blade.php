@extends('layouts.modele')
@section('title', 'Mes notes')

@section('content')
    <link rel="stylesheet" href="{{ asset('/css/controles.css') }}">

    <div class="page-background controls-background"></div>

    <div class="controls-dashboard">
        <div class="container">
            <div class="dashboard-header" data-aos="fade-down">
                <div class="student-info">
                    <h1 class="dashboard-title">Mes notes</h1>
                    <p class="student-name">{{ $student->prenom }} {{ $student->nom }}</p>
                </div>

                <div class="grade-summary">
                    <div class="average-badge {{ $moyenneGenerale >= 10 ? 'passing' : 'failing' }}">
                        <span class="average-value">{{ $moyenneGenerale ?? 'N/A' }}</span>
                        <span class="average-label">Moyenne générale</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-content">
                @if(empty($courseData))
                    <div class="empty-state" data-aos="fade-up">
                        <div class="empty-illustration">
                            <i class="bi bi-mortarboard"></i>
                        </div>
                        <h3 class="empty-title">Aucune note disponible</h3>
                        <p class="empty-description">
                            Vous n'avez pas encore de notes ou vous n'êtes inscrit à aucun cours.
                        </p>
                    </div>
                @else
                    <div class="courses-grades" data-aos="fade-up">
                        @foreach($courseData as $data)
                            <div class="course-grades-card">
                                <div class="course-header">
                                    <div class="course-title">{{ $data['course']->intitule }}</div>
                                    <div class="course-average {{ $data['moyenne'] >= 10 ? 'passing' : 'failing' }}">
                                        <span class="value">{{ $data['moyenne'] ?? 'N/A' }}</span>
                                        <span class="label">Moyenne</span>
                                    </div>
                                </div>

                                @if(empty($data['notes']))
                                    <div class="no-grades-message">
                                        <i class="bi bi-info-circle"></i>
                                        <span>Aucune note disponible pour ce cours</span>
                                    </div>
                                @else
                                    <div class="grades-list">
                                        <table class="grades-table">
                                            <thead>
                                                <tr>
                                                    <th>Contrôle</th>
                                                    <th>Date</th>
                                                    <th>Coeff.</th>
                                                    <th>Note</th>
                                                    <th>Commentaire</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data['notes'] as $gradeItem)
                                                    <tr>
                                                        <td class="control-name">
                                                            {{ $gradeItem['controle']->titre }}
                                                        </td>
                                                        <td class="control-date">
                                                            {{ \Carbon\Carbon::parse($gradeItem['controle']->date_controle)->format('d/m/Y') }}
                                                        </td>
                                                        <td class="control-coefficient">
                                                            {{ $gradeItem['controle']->coefficient }}
                                                        </td>
                                                        <td class="grade-value {{ $gradeItem['note']->note < 10 ? 'failing' : 'passing' }}">
                                                            {{ $gradeItem['note']->note }}/20
                                                        </td>
                                                        <td class="grade-comment">
                                                            {{ $gradeItem['note']->commentaire ?? '-' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            AOS.init({
                duration: 800,
                once: true,
                offset: 50
            });
        });
    </script>
@endsection