<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla: evaluation_criteria
     * 
     * Propósito: Almacena los criterios de evaluación que se utilizan para evaluar
     * los trabajos de extensión en VIEX. Cada criterio tiene un peso y una puntuación máxima.
     * 
     * Relaciones:
     * - evaluation_details: Un criterio puede estar en muchos detalles de evaluación
     */
    public function up(): void
    {
        Schema::create('evaluation_criteria', function (Blueprint $table) {
            $table->id();
            
            // Información del criterio
            $table->string('name', 200)->comment('Nombre del criterio de evaluación');
            $table->text('description')->comment('Descripción detallada del criterio');
            $table->string('category', 100)->nullable()->comment('Categoría del criterio (metodología, impacto, etc.)');
            
            // Configuración de puntuación
            $table->integer('max_score')->default(10)->comment('Puntuación máxima posible');
            $table->integer('weight')->default(1)->comment('Peso del criterio en la evaluación final');
            $table->integer('order')->default(0)->comment('Orden de visualización');
            
            // Estado
            $table->boolean('is_active')->default(true)->comment('Si el criterio está activo');
            $table->boolean('is_required')->default(true)->comment('Si es obligatorio evaluar este criterio');
            
            // Auditoría
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('is_active');
            $table->index(['is_active', 'order']);
        });
        
        // Comentario en la tabla
        //DB::statement("COMMENT ON TABLE evaluation_criteria IS 'Criterios utilizados para evaluar trabajos de extensión en VIEX'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_criteria');
    }
};
