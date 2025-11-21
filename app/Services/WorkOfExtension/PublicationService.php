<?php

namespace App\Services\WorkOfExtension;

use App\Models\WorkOfExtension;
use App\Models\User;
use App\Events\WorkPublicationAuthorized;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para manejar operaciones de publicación de trabajos de extensión
 * Gestiona la autorización de publicación de resultados
 */
class PublicationService
{
    /**
     * Autorizar o revocar publicación de resultados del trabajo
     *
     * @param WorkOfExtension $work Trabajo a autorizar
     * @param User $user Usuario que realiza la acción
     * @param bool $isAuthorized True para autorizar, false para revocar
     * @return WorkOfExtension
     */
    public function authorizePublication(WorkOfExtension $work, User $user, bool $isAuthorized): WorkOfExtension
    {
        DB::beginTransaction();

        try {
            // Actualizar consentimiento
            $work->update([
                'publication_consent' => $isAuthorized,
            ]);

            // Disparar evento para notificar a VIEX
            WorkPublicationAuthorized::dispatch($work, $user, $isAuthorized);

            Log::info($isAuthorized ? 'Publicación autorizada' : 'Publicación revocada', [
                'work_id' => $work->getKey(),
                'user_id' => $user->getKey(),
                'authorized' => $isAuthorized
            ]);

            DB::commit();

            return $work->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al procesar autorización de publicación', [
                'work_id' => $work->getKey(),
                'user_id' => $user->getKey(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}