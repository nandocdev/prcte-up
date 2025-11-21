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
        Schema::table('users', function (Blueprint $table) {
            $table->string('oracle_id')->nullable()->unique()->after('is_active')
                ->comment('Identificador único generado desde la cédula (provincia-clase-tomo-folio)');
            $table->char('oracle_estamento', 1)->nullable()->after('oracle_id')
                ->comment('Estamento del usuario en Oracle: P=Profesor, A=Administrativo, E=Estudiante');
            $table->json('oracle_data')->nullable()->after('oracle_estamento')
                ->comment('Datos completos obtenidos desde Oracle (NOMBRES, APELLIDOS, SEXO, etc.)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['oracle_id', 'oracle_estamento', 'oracle_data']);
        });
    }
};
