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
        Schema::create('institutional_project_types', function (Blueprint $table) {
            $table->id()->index('ipt_id_idx');
            $table->string('name', 255)->unique('ipt_name_uq');
            $table->text('description')->nullable();
            $table->timestamps();

            // $table->primary(['id'], 'ipt_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutional_project_types');
    }
};
