<!-- Sidebar with Rejection/Review Info -->
<div class="col-lg-3">
    <!-- Estado Actual del Trabajo -->
    <div class="card {{ $work->currentStatus->name === 'Rechazado por Coordinador' || $work->currentStatus->name === 'Rechazado por Decano/Director' ? 'card-danger' : ($work->currentStatus->name === 'Devuelto para Corrección' ? 'card-warning' : 'card-info') }}">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i>
                {{ __('Estado Actual') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="text-center mb-3">
                <span class="badge badge-lg {{ $work->currentStatus->name === 'Rechazado por Coordinador' || $work->currentStatus->name === 'Rechazado por Decano/Director' ? 'badge-danger' : ($work->currentStatus->name === 'Devuelto para Corrección' ? 'badge-warning' : 'badge-info') }}" style="font-size: 1rem; padding: 0.5rem 1rem;">
                    {{ $work->currentStatus->name }}
                </span>
            </div>

            @if($work->currentStatus->name === 'Rechazado por Coordinador' || $work->currentStatus->name === 'Rechazado por Decano/Director' || $work->currentStatus->name === 'Devuelto para Corrección')
                <div class="alert alert-{{ $work->currentStatus->name === 'Devuelto para Corrección' ? 'warning' : 'danger' }}">
                    <h6>
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>{{ $work->currentStatus->name === 'Devuelto para Corrección' ? __('Subsanaciones Solicitadas') : __('Trabajo Rechazado') }}</strong>
                    </h6>
                    <p class="mb-0 small">
                        @if($work->currentStatus->name === 'Devuelto para Corrección')
                            {{ __('Se requieren ajustes menores. Por favor, revise los comentarios del evaluador y realice las correcciones necesarias.') }}
                        @else
                            {{ __('El trabajo ha sido rechazado. Debe realizar las correcciones indicadas antes de poder reenviarlo.') }}
                        @endif
                    </p>
                </div>
            @else
                <div class="callout callout-info">
                    <p class="mb-0 small">
                        <i class="fas fa-edit"></i>
                        {{ __('Puede editar este trabajo y guardarlo como borrador.') }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Comentarios de Evaluadores -->
    @php
        // Obtener el último comentario de rechazo o subsanación
        $lastRejectionOrChange = $work->statusHistory()
            ->whereHas('toStatus', function($query) {
                $query->whereIn('name', [
                    'Rechazado por Coordinador',
                    'Rechazado por Decano/Director', 
                    'Devuelto para Corrección',
                    'Rechazado por VIEX'
                ]);
            })
            ->with(['changedBy', 'toStatus'])
            ->latest()
            ->first();
    @endphp

    @if($lastRejectionOrChange && $lastRejectionOrChange->comments)
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-comment-dots"></i>
                    {{ __('Comentarios del Evaluador') }}
                </h3>
            </div>
            <div class="card-body">
                <div class="direct-chat-msg">
                    <div class="direct-chat-infos clearfix">
                        <span class="direct-chat-name float-left">
                            {{ $lastRejectionOrChange->changedBy->name ?? __('Evaluador') }}
                        </span>
                        <span class="direct-chat-timestamp float-right">
                            {{ $lastRejectionOrChange->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <img class="direct-chat-img" src="{{ asset('vendor/adminlte/dist/img/user2-160x160.jpg') }}" alt="Evaluador">
                    <div class="direct-chat-text">
                        {{ $lastRejectionOrChange->comments }}
                    </div>
                </div>

                <hr>

                <div class="small text-muted">
                    <strong>{{ __('Fecha:') }}</strong> {{ $lastRejectionOrChange->created_at->format('d/m/Y H:i') }}<br>
                    <strong>{{ __('Estado:') }}</strong> {{ $lastRejectionOrChange->toStatus->name }}
                </div>
            </div>
        </div>
    @endif

    <!-- Historial de Revisiones -->
    @php
        $reviewHistory = $work->statusHistory()
            ->whereHas('toStatus', function($query) {
                $query->whereIn('name', [
                    'Rechazado por Coordinador',
                    'Rechazado por Decano/Director',
                    'Devuelto para Corrección',
                    'Rechazado por VIEX',
                    'Enviado a Coordinador',
                    'Enviado a Decano/Director',
                    'Enviado a VIEX'
                ]);
            })
            ->with(['changedBy', 'toStatus'])
            ->latest()
            ->limit(5)
            ->get();
    @endphp

    @if($reviewHistory->count() > 0)
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history"></i>
                    {{ __('Historial de Revisiones') }}
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($reviewHistory as $history)
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <i class="fas fa-circle text-{{ 
                                        str_contains($history->toStatus->name, 'Rechazado') ? 'danger' : 
                                        (str_contains($history->toStatus->name, 'Devuelto') ? 'warning' : 'primary') 
                                    }} small"></i>
                                    {{ $history->toStatus->name }}
                                </h6>
                                <small>{{ $history->created_at->format('d/m/Y') }}</small>
                            </div>
                            <p class="mb-1 small">
                                <strong>{{ __('Por:') }}</strong> {{ $history->changedBy->name ?? __('Sistema') }}
                            </p>
                            @if($history->comments)
                                <p class="mb-0 small text-muted">
                                    <i class="fas fa-comment"></i>
                                    {{ Str::limit($history->comments, 60) }}
                                </p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('works.show', $work) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-eye"></i>
                    {{ __('Ver Historial Completo') }}
                </a>
            </div>
        </div>
    @endif

    <!-- Instrucciones de Edición -->
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-lightbulb"></i>
                {{ __('Instrucciones') }}
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <ol class="pl-3 mb-0">
                @if($lastRejectionOrChange)
                    <li class="mb-2">{{ __('Lea cuidadosamente los comentarios del evaluador') }}</li>
                    <li class="mb-2">{{ __('Realice las correcciones indicadas en cada sección') }}</li>
                    <li class="mb-2">{{ __('Verifique que todos los campos obligatorios estén completos') }}</li>
                    <li class="mb-2">{{ __('Actualice o agregue evidencias si es necesario') }}</li>
                    <li class="mb-0">{{ __('Guarde los cambios y reenvíe el trabajo') }}</li>
                @else
                    <li class="mb-2">{{ __('Modifique los campos que desee actualizar') }}</li>
                    <li class="mb-2">{{ __('Verifique que la información sea correcta') }}</li>
                    <li class="mb-2">{{ __('Actualice archivos adjuntos si es necesario') }}</li>
                    <li class="mb-0">{{ __('Guarde los cambios cuando termine') }}</li>
                @endif
            </ol>
        </div>
    </div>

    <!-- Ayuda y Contacto -->
    <div class="card card-light">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-headset"></i>
                {{ __('¿Necesita Ayuda?') }}
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted small">{{ __('Si tiene dudas sobre las correcciones solicitadas, puede contactar a:') }}</p>
            <address class="mb-0 small">
                <strong>{{ __('Coordinación de Extensión') }}</strong><br>
                <i class="fas fa-phone"></i> {{ __('(507) 2278-xxxx') }}<br>
                <i class="fas fa-envelope"></i> extension@up.ac.pa
            </address>
        </div>
    </div>
</div>
