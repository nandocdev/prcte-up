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
            $table->id()->index('wp_id_idx');
            $table->foreignId('work_of_extension_id')->constrained('work_of_extensions');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('external_participant_name')->nullable();
            $table->string('role', 50);
            $table->timestamps();
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
