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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('type');
            $table->morphs('notifiable', 'notif_notifiable_idx');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->primary(['id'], 'notif_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
