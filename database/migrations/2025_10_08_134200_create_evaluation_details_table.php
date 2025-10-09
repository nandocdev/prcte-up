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
     * Tabla: evaluation_details
     * 
     * Propósito: Almacena la puntuación y comentarios específicos para cada criterio
     * de evaluación en una evaluación de trabajo.
     * 
     * Relaciones:
     * - work_evaluations: Evaluación global a la que pertenece
     * - evaluation_criteria: Criterio que se está evaluando
     */
    public function up(): void
    {
        Schema::create('evaluation_details', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('work_evaluation_id')
                ->constrained('work_evaluations')
                ->cascadeOnDelete()
                ->comment('Evaluación global a la que pertenece este detalle');
                
            $table->foreignId('evaluation_criteria_id')
                ->constrained('evaluation_criteria')
                ->cascadeOnDelete()
                ->comment('Criterio que se está evaluando');
            
            // Puntuación y comentarios
            $table->integer('score')
                ->comment('Puntuación otorgada (de 0 a max_score del criterio)');
                
            $table->text('comments')->nullable()
                ->comment('Comentarios específicos sobre este criterio');
                
            $table->text('evidence')->nullable()
                ->comment('Evidencia o justificación de la puntuación');
            
            // Metadatos
            $table->decimal('normalized_score', 8, 2)->nullable()
                ->comment('Puntuación normalizada (0-100)');
                
            $table->decimal('weighted_score', 8, 2)->nullable()
                ->comment('Puntuación aplicando el peso del criterio');
            
            // Auditoría
            $table->timestamps();

            // Índices
            $table->index('work_evaluation_id', 'ed_work_eval_idx');
            $table->index('evaluation_criteria_id', 'ed_criteria_idx');
            $table->index(['work_evaluation_id', 'evaluation_criteria_id'], 'ed_work_criteria_idx');

            // Constraint único: un criterio solo puede evaluarse una vez por evaluación
            $table->unique(['work_evaluation_id', 'evaluation_criteria_id'], 'ed_work_criteria_uq');
        });
        
        // Comentario en la tabla
      //   DB::statement("COMMENT ON TABLE evaluation_details IS 'Detalle de puntuación por criterio en evaluaciones de trabajos'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_details');
    }
};
