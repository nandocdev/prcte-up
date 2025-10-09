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
     * Tabla: work_evaluators
     * 
     * Propósito: Tabla pivote que relaciona trabajos de extensión con evaluadores.
     * Permite asignar uno o más evaluadores a cada trabajo y rastrear el progreso.
     * 
     * Relaciones:
     * - work_of_extensions: Trabajo que se está evaluando
     * - users: Evaluador asignado
     */
    public function up(): void
    {
        Schema::create('work_evaluators', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->unsignedBigInteger('work_of_extension_id')
                ->comment('Trabajo al que se asigna el evaluador');

            $table->unsignedBigInteger('evaluator_user_id')
                ->comment('Usuario que actúa como evaluador');

            $table->unsignedBigInteger('assigned_by_user_id')
                ->comment('Usuario de VIEX que realizó la asignación');

            // Información de la asignación
            $table->enum('role_evaluator', ['lead_evaluator', 'evaluator'])
                ->default('evaluator')
                ->comment('Rol del evaluador: lead_evaluator (principal) o evaluator (secundario)');
                
            $table->text('assignment_notes')->nullable()
                ->comment('Notas sobre la asignación');
            
            // Control de fechas
            $table->timestamp('assigned_at')->useCurrent()
                ->comment('Fecha y hora de asignación');
                
            $table->timestamp('notified_at')->nullable()
                ->comment('Fecha y hora en que se notificó al evaluador');
                
            $table->timestamp('accepted_at')->nullable()
                ->comment('Fecha y hora en que el evaluador aceptó la asignación');
                
            $table->timestamp('completed_at')->nullable()
                ->comment('Fecha y hora en que completó la evaluación');
            
            // Estado
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'declined'])
                ->default('pending')
                ->comment('Estado de la asignación');
            
            // Auditoría
            $table->timestamps();

            // Índices y llaves
            $table->primary(['id'], 'wevl_pk');
            $table->index('work_of_extension_id', 'wevl_work_idx');
            $table->index('evaluator_user_id', 'wevl_eval_idx');
            $table->index('status', 'wevl_status_idx');
            $table->index('assigned_at', 'wevl_assigned_idx');

            // Constraints
            $table->unique(['work_of_extension_id', 'evaluator_user_id'], 'wevl_work_eval_uq');
            $table->foreign('work_of_extension_id', 'wevl_work_fk')
                ->references('id')
                ->on('work_of_extensions')
                ->cascadeOnDelete();
            $table->foreign('evaluator_user_id', 'wevl_eval_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign('assigned_by_user_id', 'wevl_assigned_fk')
                ->references('id')
                ->on('users');
        });
        
        // Comentario en la tabla
      //   DB::statement("COMMENT ON TABLE work_evaluators IS 'Asignación de evaluadores a trabajos de extensión'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_evaluators');
    }
};
