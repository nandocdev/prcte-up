{{-- Modal para Rechazar Trabajo --}}
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('coordinator.reject', $work) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger">
                    <h4 class="modal-title" id="rejectModalLabel">
                        <i class="fas fa-times-circle mr-2"></i>
                        Rechazar Trabajo de Extensión
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>⚠️ Atención: Acción de Rechazo</strong>
                        <p class="mb-0 mt-2">
                            Al rechazar este trabajo, será devuelto al profesor con el estado "Rechazado por Coordinador". 
                            El profesor deberá realizar <strong>correcciones significativas</strong> antes de poder reenviarlo.
                        </p>
                    </div>

                    {{-- Diferencia entre Solicitar Cambios y Rechazar --}}
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle"></i>
                                ¿Cuál es la diferencia?
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Solicitar Subsanaciones</th>
                                        <th>Rechazar Trabajo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <span class="badge badge-warning">Correcciones Menores</span>
                                            <ul class="small mb-0 pl-3 mt-2">
                                                <li>Ajustes de formato</li>
                                                <li>Información faltante</li>
                                                <li>Evidencias por completar</li>
                                            </ul>
                                        </td>
                                        <td>
                                            <span class="badge badge-danger">Problemas Significativos</span>
                                            <ul class="small mb-0 pl-3 mt-2">
                                                <li>No cumple requisitos básicos</li>
                                                <li>Información incorrecta o falsa</li>
                                                <li>Requiere rehacer el trabajo</li>
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Motivos del Rechazo (Requerido) --}}
                    <div class="form-group">
                        <label for="rejection_reason">
                            <i class="fas fa-comment-slash"></i>
                            Motivo Detallado del Rechazo
                            <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            name="comments" 
                            id="rejection_reason" 
                            class="form-control" 
                            rows="6" 
                            required
                            minlength="20"
                            maxlength="2000"
                            placeholder="Explique claramente las razones por las cuales se rechaza este trabajo...&#10;&#10;Sea específico sobre:&#10;- Qué aspectos no cumplen con los requisitos&#10;- Por qué estos aspectos son críticos&#10;- Qué debe hacer el profesor para poder reenviar el trabajo"></textarea>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i>
                            Mínimo 20 caracteres, máximo 2000 caracteres. Sea claro y específico.
                            <span id="reject_chars_count" class="float-right">0 / 2000</span>
                        </small>
                    </div>

                    {{-- Motivos Comunes --}}
                    <div class="form-group">
                        <label>
                            <i class="fas fa-list-ul"></i>
                            Motivos Comunes de Rechazo (seleccione los que aplican)
                        </label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="reason_1" value="no_cumple_requisitos">
                                    <label class="custom-control-label small" for="reason_1">
                                        No cumple requisitos básicos del manual
                                    </label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="reason_2" value="informacion_incorrecta">
                                    <label class="custom-control-label small" for="reason_2">
                                        Información incorrecta o inconsistente
                                    </label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="reason_3" value="sin_evidencias">
                                    <label class="custom-control-label small" for="reason_3">
                                        Falta de evidencias fundamentales
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="reason_4" value="fuera_alcance">
                                    <label class="custom-control-label small" for="reason_4">
                                        Fuera del alcance de extensión
                                    </label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="reason_5" value="plagio">
                                    <label class="custom-control-label small" for="reason_5">
                                        Posible plagio o duplicación
                                    </label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="reason_6" value="normativa">
                                    <label class="custom-control-label small" for="reason_6">
                                        No cumple normativa universitaria
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Confirmación Final --}}
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="confirm_reject" required>
                        <label class="custom-control-label" for="confirm_reject">
                            <strong class="text-danger">Confirmo que he evaluado exhaustivamente este trabajo y considero que debe ser rechazado. Entiendo que el profesor deberá realizar correcciones significativas.</strong>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle mr-1"></i>
                        Confirmar Rechazo del Trabajo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Contador de caracteres para rechazo
$('#rejection_reason').on('input', function() {
    const length = $(this).val().length;
    $('#reject_chars_count').text(length + ' / 2000');
});
</script>
