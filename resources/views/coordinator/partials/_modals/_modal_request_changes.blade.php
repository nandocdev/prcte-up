{{-- Modal para Solicitar Subsanaciones --}}
<div class="modal fade" id="requestChangesModal" tabindex="-1" role="dialog" aria-labelledby="requestChangesModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('coordinator.request-changes', $work) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning">
                    <h4 class="modal-title" id="requestChangesModalLabel">
                        <i class="fas fa-edit mr-2"></i>
                        Solicitar Subsanaciones
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Solicitud de Correcciones</strong>
                        <p class="mb-0 mt-2">
                            El trabajo será devuelto al profesor para que realice las subsanaciones indicadas. 
                            Una vez corregido, el profesor podrá reenviarlo para una nueva revisión.
                        </p>
                    </div>

                    {{-- Guía para el Coordinador --}}
                    <div class="callout callout-info">
                        <h5><i class="fas fa-lightbulb"></i> Recomendaciones</h5>
                        <ul class="mb-0 pl-3 small">
                            <li>Sea específico sobre los aspectos que deben corregirse</li>
                            <li>Indique claramente qué secciones o documentos necesitan revisión</li>
                            <li>Proporcione referencias a normativas si aplica</li>
                            <li>Mantenga un tono constructivo y profesional</li>
                        </ul>
                    </div>

                    {{-- Comentarios Requeridos --}}
                    <div class="form-group">
                        <label for="change_comments">
                            <i class="fas fa-comment-dots"></i>
                            Comentarios sobre las Subsanaciones Requeridas
                            <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            name="comments" 
                            id="change_comments" 
                            class="form-control" 
                            rows="6" 
                            required
                            minlength="10"
                            maxlength="1000"
                            placeholder="Especifique claramente qué aspectos del trabajo deben ser mejorados o corregidos...&#10;&#10;Ejemplo:&#10;- Revisar la descripción del objetivo principal (falta claridad)&#10;- Adjuntar evidencias fotográficas de las actividades realizadas&#10;- Corregir las fechas de inicio y fin del proyecto"></textarea>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i>
                            Mínimo 10 caracteres, máximo 1000 caracteres.
                            <span id="chars_count" class="float-right">0 / 1000</span>
                        </small>
                    </div>

                    {{-- Plantillas Rápidas --}}
                    <div class="form-group">
                        <label>
                            <i class="fas fa-magic"></i>
                            Plantillas Rápidas (clic para insertar)
                        </label>
                        <div class="btn-group-vertical w-100" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary text-left" onclick="insertTemplate('evidences')">
                                <i class="fas fa-paperclip"></i> Falta de evidencias o documentos
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary text-left" onclick="insertTemplate('description')">
                                <i class="fas fa-align-left"></i> Descripción incompleta o poco clara
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary text-left" onclick="insertTemplate('dates')">
                                <i class="fas fa-calendar"></i> Incongruencia en fechas
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary text-left" onclick="insertTemplate('participants')">
                                <i class="fas fa-users"></i> Información de participantes incompleta
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-edit mr-1"></i>
                        Enviar Solicitud de Subsanaciones
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Contador de caracteres
$('#change_comments').on('input', function() {
    const length = $(this).val().length;
    $('#chars_count').text(length + ' / 1000');
});

// Plantillas rápidas
function insertTemplate(type) {
    const textarea = $('#change_comments');
    const templates = {
        'evidences': '- Se requiere adjuntar las evidencias documentales de las actividades realizadas (fotografías, listados de asistencia, certificados, etc.)\n',
        'description': '- La descripción del trabajo requiere mayor detalle sobre las actividades específicas realizadas y los resultados obtenidos.\n',
        'dates': '- Las fechas de inicio y finalización presentan incongruencias. Por favor, verificar y corregir.\n',
        'participants': '- La lista de participantes está incompleta. Se requiere especificar nombres completos, roles e instituciones de todos los participantes.\n'
    };
    
    textarea.val(textarea.val() + templates[type]);
    textarea.trigger('input'); // Actualizar contador
    textarea.focus();
}
</script>
