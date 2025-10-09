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
            $table->unsignedBigInteger('work_evaluation_id')
                ->comment('Evaluación global a la que pertenece este detalle');

            $table->unsignedBigInteger('evaluation_criteria_id')
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

            // Índices y constraints
            $table->primary(['id'], 'evd_pk');
            $table->index('work_evaluation_id', 'evd_work_idx');
            $table->index('evaluation_criteria_id', 'evd_criteria_idx');
            $table->index(['work_evaluation_id', 'evaluation_criteria_id'], 'evd_work_criteria_idx');

            // Constraint único: un criterio solo puede evaluarse una vez por evaluación
            $table->unique(['work_evaluation_id', 'evaluation_criteria_id'], 'evd_work_criteria_uq');
            $table->foreign('work_evaluation_id', 'evd_work_fk')
                ->references('id')
                ->on('work_evaluations')
                ->cascadeOnDelete();
            $table->foreign('evaluation_criteria_id', 'evd_criteria_fk')
                ->references('id')
                ->on('evaluation_criteria')
                ->cascadeOnDelete();
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
