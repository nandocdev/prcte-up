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
        Schema::create('technical_assistance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_of_extension_id');
            $table->string('specialization_area')->nullable();
            $table->text('expected_products')->nullable();
            $table->string('work_modality', 20);
            $table->integer('estimated_hours')->nullable();
            $table->string('assistance_type', 100);
            $table->string('collaborating_institution', 255);
            $table->text('details_json');
            $table->timestamps();

            $table->primary(['id'], 'tad_pk');
            $table->index('work_of_extension_id', 'tad_work_idx');
            $table->foreign('work_of_extension_id', 'tad_work_fk')
                ->references('id')
                ->on('work_of_extensions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_assistance');
    }
};
