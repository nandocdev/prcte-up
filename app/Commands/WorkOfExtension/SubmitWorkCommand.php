<?php

namespace App\Commands\WorkOfExtension;

use App\Models\WorkOfExtension;
use App\Models\User;
use App\Services\WorkOfExtension\SubmitWorkService;

/**
 * Comando para enviar un trabajo de extensión para revisión
 * Implementa el patrón Command para encapsular la lógica de envío
 */
class SubmitWorkCommand
{
    private WorkOfExtension $work;
    private User $user;
    private SubmitWorkService $submitService;

    public function __construct(
        WorkOfExtension $work,
        User $user,
        SubmitWorkService $submitService
    ) {
        $this->work = $work;
        $this->user = $user;
        $this->submitService = $submitService;
    }

    /**
     * Ejecutar el comando de envío
     */
    public function execute(): WorkOfExtension
    {
        // Verificar permisos del usuario
        if ($this->work->responsibleUser->getKey() !== $this->user->getKey()) {
            throw new \InvalidArgumentException('Solo el responsable del trabajo puede enviarlo para revisión.');
        }

        // Ejecutar el envío usando el servicio (el servicio maneja todas las validaciones)
        return $this->submitService->execute($this->work, $this->user);
    }

    /**
     * Crear comando desde parámetros básicos
     */
    public static function create(WorkOfExtension $work, User $user): self
    {
        return new self(
            $work,
            $user,
            app(SubmitWorkService::class)
        );
    }
}