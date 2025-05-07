@extends('layouts.modele')
@section('title', 'Planning hebdomadaire')
@section('content')
   
<link rel="stylesheet" href="{{ asset('/css/planning-etudiant.css') }}">
<link rel="stylesheet" href="{{ asset('/css/planning-calendar.css') }}">

    <div class="calendar-page">
        <div class="page-container">
            <header class="calendar-header">
                <div class="header-content">
                    <div class="header-title">
                        <h1 class="main-title">Mon planning</h1>
                        <p class="title-subtitle">Emploi du temps hebdomadaire</p>
                    </div>
                    
                    <div class="view-options">
                        <a href="{{ route('planning.student_sessionsTable') }}" class="view-option-btn">
                            <i class="bi bi-table"></i>
                            <span>Vue liste</span>
                        </a>
                    </div>
                </div>
            </header>

            <div class="calendar-controls">
                <div class="week-navigation">
                    <a href="{{ route('planning.student_planning', ['week' => $week - 1]) }}" class="nav-btn prev-btn" aria-label="Semaine précédente">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    
                    <div class="current-week">
                        <span class="week-label">Semaine</span>
                        <span class="week-number">{{ $week }}</span>
                        @php
                            $currentWeek = date('W');
                            $isCurrentWeek = ($week == $currentWeek);
                        @endphp
                        @if($isCurrentWeek)
                            <span class="current-week-badge">Actuelle</span>
                        @endif
                    </div>
                    
                    <a href="{{ route('planning.student_planning', ['week' => $week + 1]) }}" class="nav-btn next-btn" aria-label="Semaine suivante">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                
                <div class="calendar-actions">
                    <button class="action-btn today-btn" onclick="window.location.href='{{ route('planning.student_planning', ['week' => date('W')]) }}'">
                        <i class="bi bi-calendar-event"></i>
                        <span>Aujourd'hui</span>
                    </button>
                </div>
            </div>

            <div class="calendar-wrapper">
                <div class="calendar-grid">
                    <div class="time-column">
                        <div class="day-header"></div> 
                        @for ($heure = 8; $heure <= 19; $heure++)
                            <div class="time-slot">
                                <span class="time-label">{{ sprintf('%02d:00', $heure) }}</span>
                            </div>
                        @endfor
                    </div>
                    
    
                    @php
                        $daysOfWeek = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                        $today = date('N');
                        $currentWeek = date('W');
                    @endphp
                    
                    @for ($jour = 1; $jour <= 7; $jour++)
                        @php
                            $isToday = ($jour == $today && $week == $currentWeek);
                            $date = new DateTime();
                            $date->setISODate(date('Y'), $week, $jour);
                        @endphp
                        <div class="day-column {{ $isToday ? 'today' : '' }} {{ ($jour > 5) ? 'weekend' : '' }}">
                            <div class="day-header">
                                <div class="day-name">{{ $daysOfWeek[$jour-1] }}</div>
                                <div class="day-date">{{ $date->format('d/m') }}</div>
                                @if($isToday)
                                    <div class="today-indicator"></div>
                                @endif
                            </div>
                            
                            @for ($heure = 8; $heure <= 19; $heure++)
                                <div class="time-slot">
                                    @php 
                                        $sessionFound = false;
                                        $sessionData = null;
                                    @endphp
                                    
                                    @foreach ($planning as $session)
                                        @php
                                            $dateDebut = new DateTime($session->date_debut);
                                            $dateFin = new DateTime($session->date_fin);
                                            $heureDebut = $dateDebut->format('G');
                                            $minuteDebut = $dateDebut->format('i');
                                            $heureFin = $dateFin->format('G');
                                            $minuteFin = $dateFin->format('i');
                                            $dureeEnHeures = ($heureFin - $heureDebut) + ($minuteFin - $minuteDebut) / 60;
                                        @endphp
                                        
                                        @if ($dateDebut->format('W') == $week && $dateDebut->format('N') == $jour && $heureDebut == $heure)
                                            @php 
                                                $sessionFound = true; 
                                                $sessionData = $session;
                                            @endphp
                                        @endif
                                    @endforeach
                                    
                                    @if ($sessionFound)
                                        <div class="course-session" 
                                             data-duration="{{ $dureeEnHeures }}" 
                                             data-course="{{ $sessionData->intitule }}"
                                             style="--course-color: {{ generateCourseColor($sessionData->intitule) }}">
                                            <div class="course-content">
                                                <div class="course-header">
                                                    <h4 class="course-title">{{ $sessionData->intitule }}</h4>
                                                    <span class="course-time">
                                                        {{ date('H:i', strtotime($sessionData->date_debut)) }} - {{ date('H:i', strtotime($sessionData->date_fin)) }}
                                                    </span>
                                                </div>
                                                <div class="course-details">
                                                    <div class="teacher">
                                                        <i class="bi bi-person"></i>
                                                        <span>{{ $sessionData->prenom }} {{ $sessionData->nom }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    @endfor
                </div>
            </div>
            
            <div class="calendar-legend">
                <div class="legend-item">
                    <div class="legend-color today-color"></div>
                    <span>Aujourd'hui</span>
                </div>
            </div>
        </div>
    </div>
    
    @php
        function generateCourseColor($courseName) {
            $colors = [
                '#4361ee', '#3a0ca3', '#7209b7', '#f72585', '#4cc9f0',
                '#4895ef', '#560bad', '#f15bb5', '#9b5de5', '#00bbf9',
                '#00f5d4', '#fee440', '#f15bb5', '#9b5de5', '#3a86ff'
            ];
            
            $index = abs(crc32($courseName)) % count($colors);
            return $colors[$index];
        }
    @endphp
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sessions = document.querySelectorAll('.course-session');
            sessions.forEach(session => {
                const duration = parseFloat(session.getAttribute('data-duration'));
                if (duration > 0) {
                    session.style.height = `calc(${duration * 100}% - 2px)`;
                }
            });
            
            sessions.forEach(session => {
                const courseName = session.getAttribute('data-course');
                
                session.addEventListener('mouseenter', () => {
                    document.querySelectorAll(`.course-session[data-course="${courseName}"]`).forEach(s => {
                        s.classList.add('highlight');
                    });
                });
                
                session.addEventListener('mouseleave', () => {
                    document.querySelectorAll('.course-session.highlight').forEach(s => {
                        s.classList.remove('highlight');
                    });
                });
            });
            
            const todayColumn = document.querySelector('.day-column.today');
            if (todayColumn) {
                const currentHour = new Date().getHours();
                if (currentHour >= 8 && currentHour <= 19) {
                    const timeSlots = todayColumn.querySelectorAll('.time-slot');
                    const targetSlot = timeSlots[currentHour - 8];
                    if (targetSlot) {
                        const timeIndicator = document.createElement('div');
                        timeIndicator.className = 'current-time-indicator';
                        targetSlot.appendChild(timeIndicator);
                        
                        setTimeout(() => {
                            targetSlot.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }, 500);
                    }
                }
            }
        });
    </script>
@endsection