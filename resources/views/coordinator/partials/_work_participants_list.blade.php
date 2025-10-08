{{-- Lista de Participantes del Trabajo --}}
@if($work->participants && $work->participants->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users mr-2"></i>
                Participantes del Trabajo
                <span class="badge badge-info ml-2">{{ $work->participants->count() }}</span>
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Nombre Completo</th>
                        <th>Rol</th>
                        <th>Email</th>
                        <th>Institución</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($work->participants as $index => $participant)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $participant->getAttribute('name') }}</strong>
                                @if($participant->getAttribute('is_primary'))
                                    <span class="badge badge-primary ml-1">Coordinador</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ $participant->getAttribute('role') ?? 'Participante' }}
                                </span>
                            </td>
                            <td>
                                @if($participant->getAttribute('email'))
                                    <a href="mailto:{{ $participant->getAttribute('email') }}">
                                        <i class="fas fa-envelope"></i>
                                        {{ $participant->getAttribute('email') }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                {{ $participant->getAttribute('institution') ?? 'N/A' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
