<?php

namespace App\DTOs\WorkOfExtension;

use Illuminate\Http\Request;

/**
 * DTO para la creación de trabajos de extensión
 * Estructura los datos del formulario de creación
 */
class CreateWorkDTO
{
    public array $workData;
    public array $specificData;
    public string $workType;
    public int $userId;

    public function __construct(array $workData, array $specificData, string $workType, int $userId)
    {
        $this->workData = $workData;
        $this->specificData = $specificData;
        $this->workType = $workType;
        $this->userId = $userId;
    }

    /**
     * Crear DTO desde un Request de Laravel
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            workData: $request->input('work_data', []),
            specificData: $request->input('specific_data', []),
            workType: $request->input('work_type', ''),
            userId: $request->user()->getKey()
        );
    }

    /**
     * Crear DTO desde array de datos
     */
    public static function fromArray(array $data, int $userId): self
    {
        return new self(
            workData: $data['work_data'] ?? [],
            specificData: $data['specific_data'] ?? [],
            workType: $data['work_type'] ?? '',
            userId: $userId
        );
    }

    /**
     * Validar que el DTO tenga todos los datos necesarios
     */
    public function validate(): bool
    {
        if (empty($this->workData) || empty($this->workType)) {
            return false;
        }

        // Validar campos básicos requeridos
        $requiredFields = ['title', 'work_type_id', 'organizational_unit_id', 'description', 'start_date', 'end_date'];
        foreach ($requiredFields as $field) {
            if (empty($this->workData[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtener datos preparados para el servicio
     */
    public function toArray(): array
    {
        return [
            'work_data' => $this->workData,
            'specific_data' => $this->specificData,
            'work_type' => $this->workType,
            'user_id' => $this->userId,
        ];
    }
}