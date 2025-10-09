<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_type', function (Blueprint $table): void {
            $table->char('is_active', 1)
                ->default('1')
                ->comment('Indicador de disponibilidad en el catalogo (1 activo, 0 inactivo)')
                ->after('description');
            $table->index('is_active', 'work_type_is_active_idx');
        });

        Schema::table('work_statuses', function (Blueprint $table): void {
            $table->char('is_active', 1)
                ->default('1')
                ->comment('Indicador de disponibilidad en el catalogo (1 activo, 0 inactivo)')
                ->after('description');
            $table->index('is_active', 'work_statuses_is_active_idx');
        });

        Schema::table('institutional_project_types', function (Blueprint $table): void {
            $table->char('is_active', 1)
                ->default('1')
                ->comment('Indicador de disponibilidad en el catalogo (1 activo, 0 inactivo)')
                ->after('description');
            $table->index('is_active', 'institutional_project_types_is_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_type', function (Blueprint $table): void {
            $table->dropIndex('work_type_is_active_idx');
            $table->dropColumn('is_active');
        });

        Schema::table('work_statuses', function (Blueprint $table): void {
            $table->dropIndex('work_statuses_is_active_idx');
            $table->dropColumn('is_active');
        });

        Schema::table('institutional_project_types', function (Blueprint $table): void {
            $table->dropIndex('institutional_project_types_is_active_idx');
            $table->dropColumn('is_active');
        });
    }
};
