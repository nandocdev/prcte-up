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
        Schema::create('technical_assistance_details', function (Blueprint $table) {
            $table->id()->index('techasist_id_idx');
            $table->foreignId('work_of_extension_id')->constrained('work_of_extensions')->index('tech_assist_pk');
            $table->string('specialization_area')->nullable();
            $table->text('expected_products')->nullable();
            $table->string('work_modality', 20);
            $table->integer('estimated_hours')->nullable();
            $table->string('assistance_type', 100);
            $table->string('collaborating_institution', 255);
            $table->text('details_json');
            $table->timestamps();

            // $table->primary(['work_of_extension_id'], 'tech_assist_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_assistance_details');
    }
};
