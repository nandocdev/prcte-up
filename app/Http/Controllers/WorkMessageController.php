<?php

namespace App\Http\Controllers;

use App\Events\WorkMessageSent;
use App\Models\User;
use App\Models\WorkMessage;
use App\Models\WorkOfExtension;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkMessageController extends Controller
{
    /**
     * Mostrar el chat de un trabajo
     */
    public function show(WorkOfExtension $work): View
    {
        $this->authorize('view', $work);

        // Obtener coordinadores de la unidad organizacional del trabajo
        $coordinators = User::role('coordinador_extension')
            ->where('main_organizational_unit_id', $work->organizational_unit_id)
            ->get();

        // Obtener evaluadores asignados
        $evaluators = $work->evaluators ?? collect();

        // Combinar coordinadores y evaluadores como posibles destinatarios
        $possibleRecipients = $coordinators->merge($evaluators)->unique('id');

        // Cargar mensajes del usuario actual en este trabajo
        $messages = $work->messages()
            ->where(function ($query) {
                $query->where('sender_user_id', auth()->id())
                      ->orWhere('recipient_user_id', auth()->id());
            })
            ->with(['sender', 'recipient'])
            ->orderBy('created_at')
            ->get();

        // Marcar mensajes no leídos como leídos
        $work->messages()
            ->where('recipient_user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('works.messages.show', compact('work', 'messages', 'possibleRecipients'));
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

        // Verificar que el destinatario sea un coordinador o evaluador válido del trabajo
        $isValidRecipient = false;

        // Verificar si es coordinador de la unidad organizacional del trabajo
        $coordinators = User::role('coordinador_extension')
            ->where('main_organizational_unit_id', $work->organizational_unit_id)
            ->pluck('id');

        if ($coordinators->contains($request->recipient_id)) {
            $isValidRecipient = true;
        }

        // Verificar si es evaluador asignado
        if (!$isValidRecipient) {
            $isValidRecipient = $work->evaluators()->where('users.id', $request->recipient_id)->exists();
        }

        if (!$isValidRecipient) {
            return response()->json(['error' => 'Destinatario no válido. Solo puedes enviar mensajes a coordinadores o evaluadores asignados.'], 403);
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
