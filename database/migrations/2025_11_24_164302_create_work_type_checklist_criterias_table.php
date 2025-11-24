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
        Schema::create('work_type_checklist_criterias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_type_id')->constrained('work_type')->onDelete('cascade');
            $table->string('criteria_key')->unique(); // Clave única del criterio
            $table->string('name'); // Nombre del criterio
            $table->text('description')->nullable(); // Descripción detallada
            $table->string('category')->nullable(); // Categoría (datos_generales, descripcion, etc.)
            $table->integer('order')->default(0); // Orden de visualización
            $table->boolean('is_required')->default(true); // Si es obligatorio marcar
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['work_type_id', 'is_active']);
            $table->index('criteria_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_type_checklist_criterias');
    }
};
