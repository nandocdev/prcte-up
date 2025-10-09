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
        Schema::create('work_of_extensions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('work_type_id');
            $table->unsignedBigInteger('primary_responsible_user_id');
            $table->unsignedBigInteger('organizational_unit_id');
            $table->unsignedBigInteger('current_status_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('academic_period', 100)->nullable();
            $table->string('responsible_phone', 20)->nullable();
            $table->char('publication_consent', 1)->default('0');
            $table->char('is_draft', 1)->default('1');
            $table->date('submitted_at')->nullable();
            $table->timestamps();

            $table->primary(['id'], 'woe_pk');
            $table->index('work_type_id', 'woe_work_type_idx');
            $table->index('primary_responsible_user_id', 'woe_main_user_idx');
            $table->index('organizational_unit_id', 'woe_org_unit_idx');
            $table->index('current_status_id', 'woe_status_idx');
            $table->foreign('work_type_id', 'woe_work_type_fk')
                ->references('id')
                ->on('work_type');
            $table->foreign('primary_responsible_user_id', 'woe_main_user_fk')
                ->references('id')
                ->on('users');
            $table->foreign('organizational_unit_id', 'woe_org_unit_fk')
                ->references('id')
                ->on('organizational_units');
            $table->foreign('current_status_id', 'woe_status_fk')
                ->references('id')
                ->on('work_statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_of_extensions');
    }
};
