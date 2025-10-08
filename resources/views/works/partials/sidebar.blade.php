<!-- Sidebar with Info -->
<div class="col-lg-3">
    <!-- Estado del Formulario -->
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clipboard-check"></i>
                {{ __('Estado del Formulario') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="progress mb-3">
                <div class="progress-bar" role="progressbar" style="width: 0%" id="form-progress">
                    0%
                </div>
            </div>

            <div class="callout callout-info">
                <h6><i class="fas fa-info-circle"></i> {{ __('Instrucciones') }}</h6>
                <ol class="mb-0">
                    <li>{{ __('Complete la información general') }}</li>
                    <li>{{ __('Seleccione el tipo de trabajo') }}</li>
                    <li>{{ __('Complete la sección específica') }}</li>
                    <li>{{ __('Revise y guarde el borrador') }}</li>
                </ol>
            </div>

            <div class="mt-3">
                <h6><strong>{{ __('Campos Obligatorios:') }}</strong></h6>
                <ul class="list-unstyled" id="required-fields">
                    <li><i class="fas fa-square text-muted"></i> {{ __('Tipo de trabajo') }}</li>
                    <li><i class="fas fa-square text-muted"></i> {{ __('Unidad organizacional') }}</li>
                    <li><i class="fas fa-square text-muted"></i> {{ __('Título') }}</li>
                    <li><i class="fas fa-square text-muted"></i> {{ __('Descripción') }}</li>
                    <li><i class="fas fa-square text-muted"></i> {{ __('Fechas') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Ayuda Contextual -->
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-question-circle"></i>
                {{ __('Ayuda Contextual') }}
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" id="contextual-help">
            <div class="alert alert-warning">
                <p class="mb-0">
                    <strong>{{ __('Seleccione un tipo de trabajo') }}</strong>
                    {{ __('para ver información específica y campos adicionales.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Contacto de Soporte -->
    <div class="card card-light">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-headset"></i>
                {{ __('¿Necesita Ayuda?') }}
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">{{ __('Si tiene dudas sobre el llenado del formulario, puede contactar a:') }}</p>
            <address class="mb-0">
                <strong>{{ __('Vicerrectoría de Extensión') }}</strong><br>
                <i class="fas fa-phone"></i> (507) 2278-xxxx<br>
                <i class="fas fa-envelope"></i> extension@uni.edu.pa
            </address>
        </div>
    </div>
</div>
