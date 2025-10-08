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
            $table->foreignId('work_of_extension_id')->constrained('work_of_extensions');
            $table->foreignId('from_status_id')->nullable()->constrained('work_statuses');
            $table->foreignId('to_status_id')->constrained('work_statuses');
            $table->foreignId('changed_by_user_id')->constrained('users');
            $table->text('comments')->nullable();
            $table->timestamps();
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
