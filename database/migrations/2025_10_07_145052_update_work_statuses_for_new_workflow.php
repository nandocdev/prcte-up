<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Limpiar estados existentes y crear los nuevos según el flujo workflow
        DB::table('work_statuses')->truncate();
        
        // Insertar los 8 estados del nuevo flujo
        $statuses = [
            [
                'name' => 'Borrador',
                'description' => 'Trabajo en edición por el profesor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'En Revisión Coordinador',
                'description' => 'Pendiente de revisión por el Coordinador de Extensión',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'En Corrección',
                'description' => 'Devuelto al profesor para correcciones (estado transitorio)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pendiente Decano',
                'description' => 'Pendiente de aprobación por Decano/Director',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pendiente VIEX',
                'description' => 'Pendiente de evaluación por VIEX',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aprobado Internamente',
                'description' => 'Aprobado por VIEX, listo para certificación',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Certificado',
                'description' => 'Trabajo certificado oficialmente',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rechazado',
                'description' => 'Trabajo rechazado definitivamente',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('work_statuses')->insert($statuses);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar estados anteriores (básicos)
        DB::table('work_statuses')->truncate();
        
        $oldStatuses = [
            [
                'name' => 'Borrador',
                'description' => 'Trabajo en borrador',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'En Coordinador Extensión',
                'description' => 'En revisión por coordinador',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'En Decano/Director',
                'description' => 'En revisión por decano/director',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'En VIEX',
                'description' => 'En evaluación por VIEX',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Certificado',
                'description' => 'Trabajo certificado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rechazado',
                'description' => 'Trabajo rechazado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('work_statuses')->insert($oldStatuses);
    }
};
