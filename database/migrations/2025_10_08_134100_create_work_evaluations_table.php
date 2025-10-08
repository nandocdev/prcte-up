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
     * Tabla: work_evaluations
     * 
     * Propósito: Almacena la evaluación global que un evaluador hace de un trabajo.
     * Contiene comentarios generales, recomendaciones y la decisión final del evaluador.
     * 
     * Relaciones:
     * - work_of_extensions: Trabajo evaluado
     * - users: Evaluador que realiza la evaluación
     * - evaluation_details: Detalles por cada criterio
     */
    public function up(): void
    {
        Schema::create('work_evaluations', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('work_of_extension_id')
                ->constrained('work_of_extensions')
                ->cascadeOnDelete()
                ->comment('Trabajo que se está evaluando');
                
            $table->foreignId('evaluator_user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('Usuario que realiza la evaluación');
                
            $table->foreignId('work_evaluator_id')
                ->nullable()
                ->constrained('work_evaluators')
                ->nullOnDelete()
                ->comment('Referencia a la asignación del evaluador');
            
            // Contenido de la evaluación
            $table->text('general_comments')->nullable()
                ->comment('Comentarios generales sobre el trabajo');
                
            $table->text('strengths')->nullable()
                ->comment('Fortalezas identificadas');
                
            $table->text('weaknesses')->nullable()
                ->comment('Debilidades identificadas');
                
            $table->text('recommendations')->nullable()
                ->comment('Recomendaciones para el trabajo');
            
            // Puntuación y decisión
            $table->decimal('total_score', 8, 2)->nullable()
                ->comment('Puntuación total calculada');
                
            $table->decimal('weighted_score', 8, 2)->nullable()
                ->comment('Puntuación ponderada final');
                
            $table->enum('final_decision', ['approve', 'approve_with_conditions', 'reject', 'pending'])
                ->default('pending')
                ->comment('Decisión final del evaluador');
                
            $table->text('decision_justification')->nullable()
                ->comment('Justificación de la decisión');
            
            // Control de fechas
            $table->timestamp('started_at')->nullable()
                ->comment('Fecha y hora en que inició la evaluación');
                
            $table->timestamp('submitted_at')->nullable()
                ->comment('Fecha y hora en que se envió la evaluación');
            
            // Estado
            $table->enum('status', ['draft', 'in_progress', 'submitted', 'reviewed'])
                ->default('draft')
                ->comment('Estado de la evaluación');
            
            // Auditoría
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('work_of_extension_id');
            $table->index('evaluator_user_id');
            $table->index(['work_of_extension_id', 'evaluator_user_id']);
            $table->index('status');
            $table->index('final_decision');
            $table->index('submitted_at');
            
            // Constraint único: un evaluador solo puede tener una evaluación por trabajo
            $table->unique(['work_of_extension_id', 'evaluator_user_id'], 'unique_evaluation_per_work');
        });
        
        // Comentario en la tabla
      //   DB::statement("COMMENT ON TABLE work_evaluations IS 'Evaluaciones de trabajos de extensión realizadas por evaluadores'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_evaluations');
    }
};
