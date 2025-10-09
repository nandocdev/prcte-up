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
        Schema::create('activity_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_of_extension_id');
            $table->string('activity_type', 50)->nullable();
            $table->string('modality', 20)->nullable();
            $table->integer('duration_hours')->nullable();
            $table->integer('expected_participants')->nullable();
            $table->string('participant_profile')->nullable();
            $table->boolean('offers_certificate')->default(false);
            $table->text('details_json');
            $table->timestamps();

            $table->primary(['id'], 'acdtl_pk');
            $table->index('work_of_extension_id', 'acdtl_work_idx');
            $table->foreign('work_of_extension_id', 'acdtl_work_fk')
                ->references('id')
                ->on('work_of_extensions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_details');
    }
};
