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
        Schema::table('work_statuses', function (Blueprint $table) {
            $table->string('color', 7)->default('#6c757d')->after('description')
                ->comment('Color hexadecimal para identificación visual del estado');
            $table->integer('sort_order')->default(0)->after('color')
                ->comment('Orden de presentación en el flujo de trabajo');
            $table->boolean('is_final')->default(false)->after('sort_order')
                ->comment('Indica si es un estado final del flujo (certificado, rechazado)');
            
            // Índices para optimizar consultas
            $table->index('sort_order', 'ws_sort_order_idx');
            $table->index('is_final', 'ws_is_final_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_statuses', function (Blueprint $table) {
            $table->dropIndex('ws_sort_order_idx');
            $table->dropIndex('ws_is_final_idx');
            $table->dropColumn(['color', 'sort_order', 'is_final']);
        });
    }
};
