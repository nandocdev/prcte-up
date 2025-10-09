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
        Schema::create('inst_project_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique('ipt_name_uq');
            $table->text('description')->nullable();
            $table->char('is_active', 1)
                ->default('1')
                ->comment('Indicador de disponibilidad en el catalogo (1 activo, 0 inactivo)');
            $table->timestamps();

            // $table->primary(['id'], 'ipt_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inst_project_types');
    }
};
