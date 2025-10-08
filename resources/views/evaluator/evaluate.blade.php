@extends('adminlte::page')

@section('title', 'Evaluador - Evaluar Trabajo')

@section('content_header')
    <h1>
        <i class="fas fa-clipboard-list"></i> Formulario de Evaluación
        <small class="text-muted">Trabajo #{{ $work->id }}</small>
    </h1>
@stop

@section('content')
    <form method="POST" action="{{ route('evaluator.submit-evaluation', $work) }}" id="evaluationForm">
        @csrf

        {{-- Resumen del Trabajo --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt"></i> {{ $work->title }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Tipo:</strong> <span class="badge badge-info">{{ $work->workType->name }}</span></p>
                                <p><strong>Responsable:</strong> {{ $work->responsibleUser->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Unidad:</strong> {{ $work->organizationalUnit->name }}</p>
                                <p><strong>Período:</strong> {{ $work->start_date?->format('d/m/Y') }} - {{ $work->end_date?->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Evaluación por Criterios --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list-ol"></i> Evaluación por Criterios
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Instrucciones:</strong> Evalúe cada criterio asignando una puntuación.
                            Los criterios marcados con <span class="text-danger">*</span> son obligatorios.
                        </div>

                        @php
                            $currentCategory = null;
                        @endphp

                        @foreach($criteria as $criterion)
                            @if($currentCategory != $criterion->category)
                                @if($currentCategory != null)
                                    </div></div> {{-- Cerrar card anterior --}}
                                @endif
                                
                                @php $currentCategory = $criterion->category; @endphp
                                
                                <div class="card mb-3">
                                    <div class="card-header bg-secondary">
                                        <h5 class="mb-0">
                                            <i class="fas fa-folder"></i> {{ $criterion->category }}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                            @endif

                            @php
                                $detail = $evaluationDetails->get($criterion->id);
                            @endphp

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="font-weight-bold">
                                            {{ $criterion->name }}
                                            @if($criterion->is_required)
                                                <span class="text-danger">*</span>
                                            @endif
                                            <span class="badge badge-secondary ml-2">
                                                Peso: {{ $criterion->weight }}
                                            </span>
                                        </label>
                                        <p class="text-muted small">{{ $criterion->description }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Puntuación (0-{{ $criterion->max_score }})</label>
                                        <input type="number" 
                                               name="criteria[{{ $criterion->id }}][score]" 
                                               class="form-control @error("criteria.{$criterion->id}.score") is-invalid @enderror"
                                               min="0" 
                                               max="{{ $criterion->max_score }}"
                                               value="{{ old("criteria.{$criterion->id}.score", $detail->score ?? '') }}"
                                               {{ $criterion->is_required ? 'required' : '' }}
                                               placeholder="0">
                                        @error("criteria.{$criterion->id}.score")
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <label>Comentarios sobre este criterio</label>
                                        <textarea name="criteria[{{ $criterion->id }}][comments]" 
                                                  class="form-control" 
                                                  rows="2"
                                                  placeholder="Observaciones, justificación del puntaje...">{{ old("criteria.{$criterion->id}.comments", $detail->comments ?? '') }}</textarea>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <label>Evidencias o Referencias</label>
                                        <input type="text" 
                                               name="criteria[{{ $criterion->id }}][evidence]" 
                                               class="form-control" 
                                               value="{{ old("criteria.{$criterion->id}.evidence", $detail->evidence ?? '') }}"
                                               placeholder="Ej: Ver anexo 3, página 15 del informe...">
                                    </div>
                                </div>
                                <hr>
                            </div>

                        @endforeach

                        @if($currentCategory != null)
                            </div></div> {{-- Cerrar última card --}}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Evaluación General --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-comment-dots"></i> Evaluación General
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Comentarios Generales</label>
                            <textarea name="general_comments" 
                                      class="form-control @error('general_comments') is-invalid @enderror" 
                                      rows="4"
                                      placeholder="Impresión general del trabajo, contexto de la evaluación...">{{ old('general_comments', $evaluation->general_comments ?? '') }}</textarea>
                            @error('general_comments')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-success">Fortalezas del Trabajo</label>
                                    <textarea name="strengths" 
                                              class="form-control @error('strengths') is-invalid @enderror" 
                                              rows="4"
                                              placeholder="Aspectos positivos destacables...">{{ old('strengths', $evaluation->strengths ?? '') }}</textarea>
                                    @error('strengths')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-danger">Debilidades del Trabajo</label>
                                    <textarea name="weaknesses" 
                                              class="form-control @error('weaknesses') is-invalid @enderror" 
                                              rows="4"
                                              placeholder="Aspectos a mejorar...">{{ old('weaknesses', $evaluation->weaknesses ?? '') }}</textarea>
                                    @error('weaknesses')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-info">Recomendaciones</label>
                                    <textarea name="recommendations" 
                                              class="form-control @error('recommendations') is-invalid @enderror" 
                                              rows="4"
                                              placeholder="Sugerencias para el responsable...">{{ old('recommendations', $evaluation->recommendations ?? '') }}</textarea>
                                    @error('recommendations')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Decisión Final --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-gavel"></i> Decisión Final <span class="text-danger">*</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Su Decisión</label>
                            <select name="final_decision" 
                                    id="final_decision"
                                    class="form-control @error('final_decision') is-invalid @enderror" 
                                    required>
                                <option value="">-- Seleccione su decisión --</option>
                                <option value="approve" {{ old('final_decision', $evaluation->final_decision ?? '') == 'approve' ? 'selected' : '' }}>
                                    ✓ Aprobar
                                </option>
                                <option value="approve_with_conditions" {{ old('final_decision', $evaluation->final_decision ?? '') == 'approve_with_conditions' ? 'selected' : '' }}>
                                    ⚠ Aprobar con Condiciones
                                </option>
                                <option value="reject" {{ old('final_decision', $evaluation->final_decision ?? '') == 'reject' ? 'selected' : '' }}>
                                    ✗ Rechazar
                                </option>
                                <option value="pending" {{ old('final_decision', $evaluation->final_decision ?? '') == 'pending' ? 'selected' : '' }}>
                                    ⏸ Pendiente (Guardar Borrador)
                                </option>
                            </select>
                            @error('final_decision')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group" id="justification-group">
                            <label>Justificación de la Decisión <span class="text-danger">*</span></label>
                            <textarea name="decision_justification" 
                                      id="decision_justification"
                                      class="form-control @error('decision_justification') is-invalid @enderror" 
                                      rows="5"
                                      placeholder="Fundamente claramente su decisión...">{{ old('decision_justification', $evaluation->decision_justification ?? '') }}</textarea>
                            @error('decision_justification')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Es fundamental que justifique su decisión con argumentos claros y objetivos.
                            </small>
                        </div>

                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Importante:</h5>
                            <ul class="mb-0">
                                <li>Revise cuidadosamente toda la información antes de tomar su decisión.</li>
                                <li>Su evaluación debe ser objetiva, justa y fundamentada.</li>
                                <li>Una vez enviada, la evaluación no podrá ser modificada.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones de Acción --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('evaluator.show', $work) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <div>
                                <button type="submit" 
                                        name="submit_final" 
                                        value="0" 
                                        class="btn btn-warning mr-2">
                                    <i class="fas fa-save"></i> Guardar Borrador
                                </button>
                                <button type="submit" 
                                        name="submit_final" 
                                        value="1" 
                                        class="btn btn-success"
                                        id="submitFinalBtn">
                                    <i class="fas fa-paper-plane"></i> Enviar Evaluación Final
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <style>
        .form-group label {
            font-weight: 600;
        }
        .card-header h5 {
            margin-bottom: 0;
        }
        hr {
            border-top: 1px solid #dee2e6;
        }
    </style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Mostrar/ocultar justificación según decisión
        function toggleJustification() {
            const decision = $('#final_decision').val();
            const $justificationGroup = $('#justification-group');
            const $justificationTextarea = $('#decision_justification');
            
            if (decision === 'pending' || decision === '') {
                $justificationGroup.hide();
                $justificationTextarea.removeAttr('required');
            } else {
                $justificationGroup.show();
                $justificationTextarea.attr('required', 'required');
            }
        }

        // Ejecutar al cargar y al cambiar
        toggleJustification();
        $('#final_decision').on('change', toggleJustification);

        // Confirmación antes de enviar evaluación final
        $('#submitFinalBtn').on('click', function(e) {
            const decision = $('#final_decision').val();
            
            if (decision === '' || decision === 'pending') {
                e.preventDefault();
                alert('Debe seleccionar una decisión final (Aprobar, Aprobar con Condiciones o Rechazar) antes de enviar.');
                return false;
            }

            const confirmed = confirm(
                '¿Está seguro de enviar esta evaluación?\n\n' +
                'Una vez enviada, no podrá realizar modificaciones.\n' +
                'Por favor, revise toda la información antes de continuar.'
            );
            
            if (!confirmed) {
                e.preventDefault();
                return false;
            }
        });

        // Auto-guardar cada 5 minutos (opcional)
        let autoSaveInterval = null;
        
        function enableAutoSave() {
            autoSaveInterval = setInterval(function() {
                console.log('Auto-guardado activado (implementar si es necesario)');
                // Aquí se podría implementar auto-guardado via AJAX
            }, 300000); // 5 minutos
        }

        // enableAutoSave(); // Descomentar para activar
    });
</script>
@stop
