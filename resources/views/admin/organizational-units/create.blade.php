@extends('adminlte::page')

@section('title', 'Nueva Unidad Académica')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-plus"></i>
                Nueva Unidad Académica
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Configuración</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.organizational-units.index') }}">Unidades Académicas</a></li>
                <li class="breadcrumb-item active">Nueva</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-university"></i>
                        Información de la Nueva Unidad
                    </h3>
                </div>

                <form action="{{ route('admin.organizational-units.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <h5><i class="icon fas fa-ban"></i> Error en la validación</h5>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">
                                        <i class="fas fa-signature"></i>
                                        Nombre de la Unidad <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="Ej: Facultad de Ingeniería"
                                           required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">
                                        <i class="fas fa-code"></i>
                                        Código de la Unidad <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('code') is-invalid @enderror"
                                           id="code"
                                           name="code"
                                           value="{{ old('code') }}"
                                           placeholder="Ej: FI, FH, CRA"
                                           maxlength="10"
                                           required>
                                    @error('code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Código único de identificación (máximo 10 caracteres)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit_type">
                                        <i class="fas fa-layer-group"></i>
                                        Tipo de Unidad <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('unit_type') is-invalid @enderror"
                                            id="unit_type"
                                            name="unit_type"
                                            required>
                                        <option value="">-- Seleccionar tipo --</option>
                                        <option value="faculty" {{ old('unit_type') == 'faculty' ? 'selected' : '' }}>
                                            🏛️ Facultad
                                        </option>
                                        <option value="department" {{ old('unit_type') == 'department' ? 'selected' : '' }}>
                                            🏢 Departamento
                                        </option>
                                        <option value="school" {{ old('unit_type') == 'school' ? 'selected' : '' }}>
                                            🎓 Escuela
                                        </option>
                                        <option value="center" {{ old('unit_type') == 'center' ? 'selected' : '' }}>
                                            📍 Centro Regional
                                        </option>
                                    </select>
                                    @error('unit_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent_id">
                                        <i class="fas fa-sitemap"></i>
                                        Unidad Padre (Opcional)
                                    </label>
                                    <select class="form-control @error('parent_id') is-invalid @enderror"
                                            id="parent_id"
                                            name="parent_id">
                                        <option value="">-- Sin unidad padre --</option>
                                        @foreach($parentUnits as $parent)
                                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }} ({{ $parent->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Seleccionar si esta unidad pertenece a otra unidad académica
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">
                                <i class="fas fa-align-left"></i>
                                Descripción (Opcional)
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Descripción detallada de la unidad académica">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input"
                                       type="checkbox"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="custom-control-label">
                                    <i class="fas fa-toggle-on"></i>
                                    Unidad activa
                                </label>
                                <small class="form-text text-muted">
                                    Solo las unidades activas aparecerán disponibles para asignación
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Crear Unidad Académica
                                </button>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('admin.organizational-units.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .form-group label {
            font-weight: 600;
        }
        .card-primary:not(.card-outline) .card-header {
            background-color: #007bff;
        }
    </style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Auto-generate code from name
        $('#name').on('input', function() {
            let name = $(this).val();
            let code = generateCode(name);
            $('#code').val(code);
        });

        function generateCode(name) {
            // Extract initials from name
            let words = name.split(' ');
            let code = '';

            words.forEach(function(word) {
                if (word.length > 0) {
                    code += word.charAt(0).toUpperCase();
                }
            });

            return code.substring(0, 10); // Limit to 10 characters
        }

        // Form validation
        $('form').submit(function(e) {
            let hasErrors = false;

            // Check required fields
            $('input[required], select[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    hasErrors = true;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (hasErrors) {
                e.preventDefault();
                toastr.error('Por favor, complete todos los campos obligatorios.');
            }
        });
    });
</script>
@stop
