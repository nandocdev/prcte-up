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
        Schema::create('project_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_of_extension_id');
            $table->string('project_category', 50);
            $table->unsignedBigInteger('institutional_project_type_id')->nullable();
            $table->text('objectives')->nullable();
            $table->text('methodology')->nullable();
            $table->integer('direct_beneficiaries')->nullable();
            $table->integer('indirect_beneficiaries')->nullable();
            $table->string('geographic_area')->nullable();
            $table->text('details_json');
            $table->text('schedule_json')->nullable();
            $table->text('resources_json')->nullable();
            $table->text('costs_json')->nullable();
            $table->unsignedBigInteger('ss_tutor_user_id')->nullable();
            $table->text('ss_intervention_summary')->nullable();
            $table->timestamps();

            $table->primary(['id'], 'pd_pk');
            $table->index('work_of_extension_id', 'pd_work_idx');
            $table->index('institutional_project_type_id', 'pd_inst_type_idx');
            $table->index('ss_tutor_user_id', 'pd_ss_tutor_idx');
            $table->foreign('work_of_extension_id', 'pd_work_fk')
                ->references('id')
                ->on('work_of_extensions');
            $table->foreign('institutional_project_type_id', 'pd_inst_type_fk')
                ->references('id')
                ->on('inst_project_types');
            $table->foreign('ss_tutor_user_id', 'pd_ss_tutor_fk')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_details');
    }
};
