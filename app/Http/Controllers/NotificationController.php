<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controlador para la gestión de notificaciones del sistema.
 *
 * Maneja la visualización y marcado como leídas de las notificaciones
 * generadas por el sistema de flujo de trabajo de extensiones.
 */
class NotificationController extends Controller {
    /**
     * Muestra todas las notificaciones del usuario autenticado.
     */
    public function index(): View {
        $user = Auth::user();

        // Usar el método notifications() que existe por el trait Notifiable
        // @phpstan-ignore-next-line action_url
        $notifications = $user->notifications()
            ->orderBy('read_at', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // @phpstan-ignore-next-line
        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Marca una notificación específica como leída.
     */
    public function markAsRead(string $notificationId): JsonResponse {
        $user = Auth::user();

        // @phpstan-ignore-next-line
        $notification = $user->notifications()->find($notificationId);

        if ($notification instanceof DatabaseNotification && $notification->getAttribute('read_at') === null) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Marca todas las notificaciones como leídas.
     */
    public function markAllAsRead(): JsonResponse {
        $user = Auth::user();

        // @phpstan-ignore-next-line
        $user->unreadNotifications()->markAsRead();

        return response()->json([
            'success' => true,
            'message' => __('Todas las notificaciones han sido marcadas como leídas.')
        ]);
    }
}
