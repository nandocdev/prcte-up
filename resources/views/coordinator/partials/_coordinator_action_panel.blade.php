{{-- Panel de Acciones del Coordinador --}}
@can('reviewAsCoordinator', $work)
    @php
        $currentStatus = $work->currentStatus->name ?? '';
        // Estados que permiten acción del coordinador (nombres exactos de BD)
        $canTakeAction = in_array($currentStatus, [
            'Enviado a Coordinador',
            'En Revisión Coordinador',
            'Devuelto para Corrección' // También puede revisar trabajos devueltos
        ]);
    @endphp



    @if($canTakeAction)
        {{-- Trabajo Pendiente de Revisión --}}
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-2"></i>
                    Acciones de Coordinación
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Trabajo pendiente de revisión</strong>
                    <p class="mb-0 mt-2 small">
                        Este trabajo está esperando su revisión como coordinador de extensión. 
                        Por favor, revise cuidadosamente toda la información antes de tomar una decisión.
                    </p>
                </div>

                {{-- Estadísticas Rápidas --}}
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-paperclip"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-number">{{ $work->getMedia('attachments')->count() }}</span>
                                <span class="info-box-text">Archivos</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-number">{{ $work->participants->count() ?? 0 }}</span>
                                <span class="info-box-text">Personas</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                            <div class="info-box-content">
                                @php
                                    $daysPending = $work->getAttribute('submitted_at') 
                                        ? $work->getAttribute('submitted_at')->diffInDays(now()) 
                                        : 0;
                                @endphp
                                <span class="info-box-number">{{ $daysPending }}</span>
                                <span class="info-box-text">Días</span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- Botones de Acción Principales --}}
                <div class="btn-group-vertical w-100" role="group">
                    {{-- Aprobar Trabajo --}}
                    <button type="button" 
                            class="btn btn-success btn-lg mb-2 {{ !$checklistComplete ? 'disabled' : '' }}" 
                            data-toggle="modal" 
                            data-target="#approveModal"
                            {{ !$checklistComplete ? 'title="Complete el checklist antes de aprobar"' : '' }}>
                        <i class="fas fa-check-circle mr-2"></i>
                        Aprobar y Enviar al Decano/Director
                        @if(!$checklistComplete)
                            <i class="fas fa-exclamation-triangle ml-2" title="Checklist incompleto"></i>
                        @endif
                    </button>

                    {{-- Solicitar Subsanaciones --}}
                    <button type="button" 
                            class="btn btn-warning btn-lg mb-2" 
                            data-toggle="modal" 
                            data-target="#requestChangesModal">
                        <i class="fas fa-edit mr-2"></i>
                        Solicitar Subsanaciones
                    </button>

                    {{-- Rechazar Trabajo --}}
                    <button type="button" 
                            class="btn btn-danger btn-lg" 
                            data-toggle="modal" 
                            data-target="#rejectModal">
                        <i class="fas fa-times-circle mr-2"></i>
                        Rechazar Trabajo
                    </button>
                </div>

                {{-- Ayuda Contextual --}}
                <div class="mt-3">
                    <div class="callout callout-info">
                        <h5><i class="fas fa-question-circle"></i> Ayuda</h5>
                        <ul class="mb-0 pl-3 small">
                            <li><strong>Aprobar:</strong> El trabajo avanza al Decano/Director</li>
                            <li><strong>Solicitar Subsanaciones:</strong> Devuelve al profesor para correcciones menores</li>
                            <li><strong>Rechazar:</strong> Devuelve al profesor con problemas significativos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- Trabajo Ya Procesado --}}
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Estado del Trabajo
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-light">
                    <i class="fas fa-check-circle text-success"></i>
                    <strong>Trabajo procesado</strong>
                    <p class="mb-0 mt-2 small">
                        Este trabajo ya ha sido procesado y no requiere acciones adicionales de su parte.
                    </p>
                </div>

                <dl class="row">
                    <dt class="col-sm-5">Estado actual:</dt>
                    <dd class="col-sm-7">
                        <span class="badge badge-info">{{ $currentStatus }}</span>
                    </dd>

                    @php
                        $lastAction = $work->statusHistory
                            ->where('changed_by_user_id', auth()->id())
                            ->sortByDesc('created_at')
                            ->first();
                    @endphp

                    @if($lastAction)
                        <dt class="col-sm-5">Tu última acción:</dt>
                        <dd class="col-sm-7">
                            {{ $lastAction->status->name }}<br>
                            <small class="text-muted">
                                {{ $lastAction->created_at->format('d/m/Y H:i') }}
                            </small>
                        </dd>

                        @if($lastAction->comments)
                            <dt class="col-sm-5">Comentarios:</dt>
                            <dd class="col-sm-7">
                                <small>{{ Str::limit($lastAction->comments, 100) }}</small>
                            </dd>
                        @endif
                    @endif
                </dl>
            </div>
        </div>
    @endif
@endcan

