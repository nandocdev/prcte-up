@extends('adminlte::page')

@section('title', 'Mensajes - ' . $work->title)

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">
            <i class="fas fa-comments text-primary"></i>
            Chat del Trabajo
        </h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('works.index') }}">Trabajos</a></li>
            <li class="breadcrumb-item"><a href="{{ route('works.show', $work) }}">{{ Str::limit($work->title, 20) }}</a></li>
            <li class="breadcrumb-item active">Mensajes</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-comments"></i>
                    Comunicación con Evaluadores
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                {{-- Lista de evaluadores --}}
                <div class="mb-3">
                    <h5>Destinatarios disponibles:</h5>
                    <div class="row">
                        @forelse($possibleRecipients as $recipient)
                        <div class="col-md-4 mb-2">
                            <div class="card border-info">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <strong>{{ $recipient->name }}</strong><br>
                                            <small class="text-muted">{{ $recipient->email }}</small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary start-chat-btn"
                                                data-recipient-id="{{ $recipient->id }}"
                                                data-recipient-name="{{ $recipient->name }}">
                                            <i class="fas fa-comment"></i> Chatear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No hay destinatarios disponibles para este trabajo.
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Área de chat --}}
                <div id="chat-area" class="d-none">
                    <hr>
                    <div class="chat-container" style="height: 400px; border: 1px solid #ddd; border-radius: 5px; padding: 10px; overflow-y: auto; background-color: #f8f9fa;">
                        <div id="chat-messages" class="chat-messages">
                            {{-- Mensajes se cargarán aquí --}}
                        </div>
                    </div>

                    {{-- Formulario para enviar mensaje --}}
                    <div class="mt-3">
                        <form id="send-message-form">
                            @csrf
                            <div class="input-group">
                                <textarea class="form-control" id="message-input" name="message" rows="2" placeholder="Escribe tu mensaje..." required></textarea>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-paper-plane"></i> Enviar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Historial de mensajes --}}
        @if($messages->count() > 0)
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history"></i>
                    Historial de Mensajes
                </h3>
            </div>
            <div class="card-body">
                <div class="timeline timeline-inverse">
                    @foreach($messages as $message)
                    <div class="time-label">
                        <span class="bg-info">{{ $message->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div>
                        <i class="fas fa-envelope bg-primary"></i>
                        <div class="timeline-item">
                            <span class="time">
                                <i class="far fa-clock"></i>
                                {{ $message->created_at->diffForHumans() }}
                            </span>
                            <h3 class="timeline-header">
                                Mensaje {{ $message->isFrom(auth()->user()) ? 'enviado' : 'recibido' }}
                                @if($message->isFrom(auth()->user()))
                                    a {{ $message->recipient->name }}
                                @else
                                    de {{ $message->sender->name }}
                                @endif
                            </h3>
                            <div class="timeline-body">
                                <div class="card {{ $message->isFrom(auth()->user()) ? 'border-primary' : 'border-success' }}">
                                    <div class="card-body py-2">
                                        <p class="mb-0">{{ $message->message }}</p>
                                        @if(!$message->is_read && !$message->isFrom(auth()->user()))
                                        <small class="text-warning">
                                            <i class="fas fa-circle"></i> No leído
                                        </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Modal para seleccionar destinatario --}}
<div class="modal fade" id="recipient-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Destinatario</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Selecciona con quién deseas chatear:</p>
                <div id="recipient-list">
                    {{-- Lista se cargará dinámicamente --}}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    let currentRecipientId = null;
    let currentRecipientName = null;

    // Iniciar chat con evaluador
    $('.start-chat-btn').on('click', function() {
        currentRecipientId = $(this).data('recipient-id');
        currentRecipientName = $(this).data('recipient-name');

        // Mostrar área de chat
        $('#chat-area').removeClass('d-none');

        // Cargar mensajes
        loadMessages();

        // Scroll al final
        scrollToBottom();
    });

    // Enviar mensaje
    $('#send-message-form').on('submit', function(e) {
        e.preventDefault();

        const message = $('#message-input').val().trim();
        if (!message || !currentRecipientId) return;

        $.ajax({
            url: '{{ route("works.messages.store", $work) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                message: message,
                recipient_id: currentRecipientId
            },
            success: function(response) {
                if (response.success) {
                    $('#message-input').val('');
                    loadMessages();
                    scrollToBottom();

                    // Mostrar notificación de éxito
                    toastr.success('Mensaje enviado correctamente');
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.error || 'Error al enviar mensaje';
                toastr.error(error);
            }
        });
    });

    // Cargar mensajes
    function loadMessages() {
        if (!currentRecipientId) return;

        $.ajax({
            url: '{{ route("works.messages.show", $work) }}',
            method: 'GET',
            data: {
                recipient_id: currentRecipientId
            },
            success: function(response) {
                // Aquí deberíamos renderizar los mensajes filtrados
                // Por simplicidad, recargamos la página o implementamos polling
            }
        });
    }

    // Función para hacer scroll al final del chat
    function scrollToBottom() {
        const chatContainer = $('.chat-container');
        chatContainer.scrollTop(chatContainer[0].scrollHeight);
    }

    // Polling para nuevos mensajes (cada 30 segundos)
    setInterval(function() {
        if (currentRecipientId) {
            loadMessages();
        }
    }, 30000);
});
</script>
@stop