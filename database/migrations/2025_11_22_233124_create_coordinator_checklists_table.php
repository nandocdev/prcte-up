<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coordinator_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_of_extension_id')->constrained()->onDelete('cascade');
            $table->foreignId('coordinator_id')->constrained('users')->onDelete('cascade');
            $table->json('checklist_data'); // Almacena el estado completo del checklist
            $table->text('reviewer_notes')->nullable(); // Notas del revisor
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            // Índices para optimización
            $table->index(['work_of_extension_id', 'coordinator_id']);
            $table->index('last_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordinator_checklists');
    }
};
