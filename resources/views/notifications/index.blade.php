@extends('adminlte::page')

@section('title', 'Notificaciones')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>
        <i class="fas fa-bell mr-2"></i>
        {{ __('Notificaciones') }}
    </h1>
    @if($unreadCount > 0)
    <button class="btn btn-outline-primary btn-sm" id="markAllAsRead">
        <i class="fas fa-check-double mr-1"></i>
        {{ __('Marcar todas como leídas') }}
    </button>
    @endif
</div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-inbox mr-2"></i>
                    {{ __('Mis Notificaciones') }}
                    @if($unreadCount > 0)
                    <span class="badge badge-warning ml-2">{{ $unreadCount }} {{ __('sin leer') }}</span>
                    @endif
                </h3>
            </div>
            <div class="card-body p-0">
                @forelse($notifications as $notification)
                <div class="notification-item {{ is_null($notification->read_at) ? 'unread' : 'read' }}"
                    data-notification-id="{{ $notification->id }}">
                    <div class="d-flex p-3 border-bottom">
                        <!-- Icono de estado -->
                        <div class="mr-3">
                            @if(is_null($notification->read_at))
                            <span class="badge badge-warning badge-pill">
                                <i class="fas fa-circle" style="font-size: 8px;"></i>
                            </span>
                            @else
                            <span class="text-muted">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            @endif
                        </div>

                        <!-- Contenido de la notificación -->
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="mb-0 {{ is_null($notification->read_at) ? 'font-weight-bold' : '' }}">
                                    {{ $notification->data['title'] ?? __('Notificación del Sistema') }}
                                </h6>
                                <small class="text-muted">
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>

                            <p class="mb-1 text-muted">
                                {{ $notification->data['message'] ?? __('Sin mensaje') }}
                            </p>

                            @if(isset( $notification->data['action_url']))
                            <a href="{{ $notification->data['action_url'] }}"
                                class="btn btn-sm btn-outline-primary notification-action"
                                data-notification-id="{{ $notification->id }}">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                {{ $notification->data['action_text'] ?? __('Ver detalles') }}
                            </a>
                            @endif

                            @if(is_null($notification->read_at))
                            <button class="btn btn-sm btn-outline-secondary ml-2 mark-as-read-btn"
                                data-notification-id="{{ $notification->id }}">
                                <i class="fas fa-check mr-1"></i>
                                {{ __('Marcar como leída') }}
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center p-4">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('No tienes notificaciones') }}</h5>
                    <p class="text-muted">{{ __('Todas las notificaciones del sistema aparecerán aquí.') }}</p>
                </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
            <div class="card-footer">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .notification-item.unread {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
    }

    .notification-item.read {
        background-color: #ffffff;
        opacity: 0.8;
    }

    .notification-item:hover {
        background-color: #f1f3f4;
    }

    .notification-action,
    .mark-as-read-btn {
        font-size: 0.875rem;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Marcar notificación individual como leída
        $('.mark-as-read-btn').on('click', function() {
            const notificationId = $(this).data('notification-id');
            const button = $(this);
            const notificationItem = button.closest('.notification-item');

            $.post(`/notifications/${notificationId}/mark-as-read`, {
                    _token: '{{ csrf_token() }}'
                })
                .done(function() {
                    // Actualizar visualmente la notificación
                    notificationItem.removeClass('unread').addClass('read');
                    button.fadeOut();

                    // Actualizar contador de no leídas
                    updateUnreadCount();

                    // Mostrar mensaje de éxito
                    toastr.success('{{ __("Notificación marcada como leída") }}');
                })
                .fail(function() {
                    toastr.error('{{ __("Error al marcar notificación como leída") }}');
                });
        });

        // Marcar todas como leídas
        $('#markAllAsRead').on('click', function() {
            const button = $(this);

            $.post('/notifications/mark-all-as-read', {
                    _token: '{{ csrf_token() }}'
                })
                .done(function(data) {
                    // Actualizar visualmente todas las notificaciones
                    $('.notification-item.unread').removeClass('unread').addClass('read');
                    $('.mark-as-read-btn').fadeOut();
                    button.fadeOut();

                    // Actualizar contador
                    updateUnreadCount();

                    // Mostrar mensaje de éxito
                    toastr.success(data.message);
                })
                .fail(function() {
                    toastr.error('{{ __("Error al marcar todas las notificaciones como leídas") }}');
                });
        });

        // Marcar como leída al hacer clic en acción
        $('.notification-action').on('click', function() {
            const notificationId = $(this).data('notification-id');

            $.post(`/notifications/${notificationId}/mark-as-read`, {
                _token: '{{ csrf_token() }}'
            });
        });

        function updateUnreadCount() {
            const unreadCount = $('.notification-item.unread').length;
            if (unreadCount === 0) {
                $('.badge').text('0 {{ __("sin leer") }}').hide();
            } else {
                $('.badge').text(`${unreadCount} {{ __("sin leer") }}`);
            }
        }
    });
</script>
@stop