<?php

namespace App\Http\Controllers;

use App\Events\WorkMessageSent;

class WorkMessageController extends Controller
{
    /**
     * Mostrar el chat de un trabajo
     */
    public function show(WorkOfExtension $work): View
    {
        $this->authorize('view', $work);

        // Marcar mensajes como leídos para el usuario actual
        $work->messages()
            ->where('recipient_user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $messages = $work->messages()
            ->with(['sender', 'recipient'])
            ->orderBy('created_at')
            ->get();

        return view('works.messages.show', compact('work', 'messages'));
    }

    /**
     * Enviar un mensaje
     */
    public function store(Request $request, WorkOfExtension $work): JsonResponse
    {
        $this->authorize('update', $work); // Solo el propietario puede enviar mensajes

        $request->validate([
            'message' => 'required|string|max:1000',
            'recipient_id' => 'required|exists:users,id',
        ]);

        // Verificar que el destinatario sea un evaluador válido del trabajo
        $isValidRecipient = $work->evaluators()->where('users.id', $request->recipient_id)->exists();

        if (!$isValidRecipient) {
            return response()->json(['error' => 'Destinatario no válido'], 403);
        }

        $recipient = User::findOrFail($request->recipient_id);

        $message = WorkMessage::create([
            'work_of_extension_id' => $work->id,
            'sender_user_id' => auth()->id(),
            'recipient_user_id' => $request->recipient_id,
            'message' => $request->message,
        ]);

        // Disparar evento para notificaciones
        WorkMessageSent::dispatch($message, $work, auth()->user(), $recipient);

        // Cargar relaciones para la respuesta
        $message->load(['sender', 'recipient']);

        return response()->json([
            'message' => $message,
            'success' => true
        ]);
    }

    /**
     * Obtener mensajes no leídos (para notificaciones)
     */
    public function unread(Request $request): JsonResponse
    {
        $userId = auth()->id();

        $unreadMessages = WorkMessage::where('recipient_user_id', $userId)
            ->where('is_read', false)
            ->with(['work', 'sender'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'unread_count' => $unreadMessages->count(),
            'messages' => $unreadMessages
        ]);
    }

    /**
     * Marcar mensaje como leído
     */
    public function markAsRead(WorkMessage $message): JsonResponse
    {
        // Verificar que el usuario sea el destinatario
        if ($message->recipient_user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }
}
