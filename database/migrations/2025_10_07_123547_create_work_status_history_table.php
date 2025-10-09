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
        Schema::create('work_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_of_extension_id');
            $table->unsignedBigInteger('from_status_id')->nullable();
            $table->unsignedBigInteger('to_status_id');
            $table->unsignedBigInteger('changed_by_user_id');
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->primary(['id'], 'wsh_pk');
            $table->index('work_of_extension_id', 'wsh_work_idx');
            $table->index('from_status_id', 'wsh_from_status_idx');
            $table->index('to_status_id', 'wsh_to_status_idx');
            $table->index('changed_by_user_id', 'wsh_user_idx');
            $table->foreign('work_of_extension_id', 'wsh_work_fk')
                ->references('id')
                ->on('work_of_extensions');
            $table->foreign('from_status_id', 'wsh_from_status_fk')
                ->references('id')
                ->on('work_statuses');
            $table->foreign('to_status_id', 'wsh_to_status_fk')
                ->references('id')
                ->on('work_statuses');
            $table->foreign('changed_by_user_id', 'wsh_user_fk')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_status_history');
    }
};
