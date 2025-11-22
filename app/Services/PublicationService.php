<?php

namespace App\Services;

use App\Models\WorkOfExtension;
use App\Models\User;
use App\Events\PublicationAuthorizationChanged;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para manejar autorizaciones de publicación de trabajos de extensión
 */
class PublicationService
{
    /**
     * Autorizar o revocar publicación de resultados del trabajo
     *
     * @param WorkOfExtension $work Trabajo de extensión
     * @param User $user Usuario que realiza la acción
     * @param bool $isAuthorized True para autorizar, false para revocar
     * @return void
     */
    public function authorizePublication(WorkOfExtension $work, User $user, bool $isAuthorized): void
    {
        // Verificar que el usuario es el responsable del trabajo
        if ($work->getAttribute('primary_responsible_user_id') !== $user->getKey()) {
            throw new \InvalidArgumentException(__('Solo el responsable del trabajo puede autorizar su publicación.'));
        }

        // Verificar que el trabajo está certificado
        if (!$work->isCertified()) {
            throw new \InvalidArgumentException(__('Solo se puede autorizar publicación de trabajos certificados.'));
        }

        // Actualizar el campo de autorización
        $work->update([
            'publication_consent' => $isAuthorized,
            'publication_authorized_at' => $isAuthorized ? now() : null,
        ]);

        // Disparar evento para notificaciones
        PublicationAuthorizationChanged::dispatch($work, $user, $isAuthorized);

        Log::info($isAuthorized ? 'Publicación autorizada' : 'Publicación revocada', [
            'work_id' => $work->getKey(),
            'work_title' => $work->getAttribute('title'),
            'authorized_by' => $user->getKey(),
            'user_name' => $user->getAttribute('name'),
        ]);
    }
}