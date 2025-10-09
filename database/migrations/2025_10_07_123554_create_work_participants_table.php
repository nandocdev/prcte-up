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
        Schema::create('work_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_of_extension_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('external_participant_name')->nullable();
            $table->string('role', 50);
            $table->timestamps();

            $table->primary(['id'], 'wp_pk');
            $table->index('work_of_extension_id', 'wp_work_idx');
            $table->index('user_id', 'wp_user_idx');
            $table->foreign('work_of_extension_id', 'wp_work_fk')
                ->references('id')
                ->on('work_of_extensions');
            $table->foreign('user_id', 'wp_user_fk')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_participants');
    }
};
