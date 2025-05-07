@props(['type' => 'info', 'message' => null, 'dismissible' => true])

@php

    $type = match($type) {
        'error' => 'danger',
        'success', 'danger', 'warning', 'info' => $type,
        default => 'info'
    };
    
    $icon = match($type) {
        'success' => 'bi-check-circle-fill',
        'danger' => 'bi-exclamation-triangle-fill',
        'warning' => 'bi-exclamation-circle-fill',
        'info' => 'bi-info-circle-fill',
        default => 'bi-info-circle-fill'
    };
    
    $alertId = 'alert_' . uniqid();
@endphp

@if($message || $slot->isNotEmpty())
    <div id="{{ $alertId }}" class="alert alert-{{ $type }} fade-in">
        <div class="alert-icon">
            <i class="bi {{ $icon }}"></i>
        </div>
        <div class="alert-content">
            <p>{{ $message ?? $slot }}</p>
        </div>
        @if($dismissible)
            <button onclick="closeAlert('{{ $alertId }}')" class="alert-close">
                <i class="bi bi-x-lg"></i>
            </button>
        @endif
    </div>

    @if($dismissible)
        <script>
            function closeAlert(alertId) {
                const alert = document.getElementById(alertId);
                alert.classList.add('fade-out');
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }
            
            setTimeout(() => {
                const alert = document.getElementById('{{ $alertId }}');
                if (alert) {
                    alert.classList.add('fade-out');
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 500);
                }
            }, 5000);
        </script>
    @endif
@endif