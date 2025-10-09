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
            $table->foreignId('work_of_extension_id')->constrained('work_of_extensions')->index('proj_details_work_idx');
            $table->string('project_category', 50);
            $table->foreignId('institutional_project_type_id')->nullable()->constrained('institutional_project_types');
            $table->text('objectives')->nullable();
            $table->text('methodology')->nullable();
            $table->integer('direct_beneficiaries')->nullable();
            $table->integer('indirect_beneficiaries')->nullable();
            $table->string('geographic_area')->nullable();
            $table->text('details_json');
            $table->text('schedule_json')->nullable();
            $table->text('resources_json')->nullable();
            $table->text('costs_json')->nullable();
            $table->foreignId('ss_tutor_user_id')->nullable()->constrained('users');
            $table->text('ss_intervention_summary')->nullable();
            $table->timestamps();
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
