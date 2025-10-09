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
        Schema::create('work_type', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique('wt_name_uq')->index('wt_name_idx');
            $table->text('description')->nullable();
            $table->char('is_active', 1)
                ->default('1')
                ->comment('Indicador de disponibilidad en el catalogo (1 activo, 0 inactivo)');
            $table->timestamps();

            $table->primary(['id'], 'wt_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_type');
    }
};
