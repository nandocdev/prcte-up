{{-- Modal para Aprobar Trabajo --}}
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('coordinator.approve', $work) }}" method="POST" data-clear-notes="true">
                @csrf
                <div class="modal-header bg-success">
                    <h4 class="modal-title" id="approveModalLabel">
                        <i class="fas fa-check-circle mr-2"></i>
                        Aprobar Trabajo de Extensión
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fas fa-info-circle"></i>
                        <strong>Confirmación de Aprobación</strong>
                        <p class="mb-0 mt-2">
                            Al aprobar este trabajo, será enviado automáticamente a VIEX para evaluación final. 
                            Se notificará por email a VIEX y al profesor responsable.
                        </p>
                    </div>

                    {{-- Resumen del Trabajo --}}
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-alt"></i>
                                Resumen del Trabajo
                            </h5>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Título:</dt>
                                <dd class="col-sm-8">{{ $work->getAttribute('title') }}</dd>

                                <dt class="col-sm-4">Tipo:</dt>
                                <dd class="col-sm-8">{{ $work->workType->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-4">Profesor:</dt>
                                <dd class="col-sm-8">{{ $work->responsibleUser->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-4">Unidad:</dt>
                                <dd class="col-sm-8">{{ $work->organizationalUnit->name ?? 'N/A' }}</dd>
                            </dl>
                        </div>
                    </div>

                    {{-- Comentarios Opcionales --}}
                    <div class="form-group">
                        <label for="approval_comments">
                            <i class="fas fa-comment-alt"></i>
                            Comentarios de Aprobación (opcional)
                        </label>
                        <textarea 
                            name="comments" 
                            id="approval_comments" 
                            class="form-control" 
                            rows="4"
                            placeholder="Puede agregar comentarios sobre la aprobación, observaciones positivas, o recomendaciones para VIEX..."></textarea>
                        <small class="form-text text-muted">
                            Estos comentarios serán visibles para VIEX y el profesor.
                        </small>
                    </div>

                    {{-- Confirmación --}}
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="confirm_approve" required>
                        <label class="custom-control-label" for="confirm_approve">
                            <strong>Confirmo que he revisado completamente este trabajo y considero que cumple con los requisitos para ser enviado a VIEX.</strong>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle mr-1"></i>
                        Confirmar Aprobación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
