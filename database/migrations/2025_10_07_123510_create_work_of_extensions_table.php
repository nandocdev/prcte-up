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
            $table->foreignId('work_type_id')->constrained('work_type');
            $table->foreignId('primary_responsible_user_id')->constrained('users');
            $table->foreignId('organizational_unit_id')->constrained('organizational_units');
            $table->foreignId('current_status_id')->constrained('work_statuses');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('academic_period', 100)->nullable();
            $table->string('responsible_phone', 20)->nullable();
            $table->char('publication_consent', 1)->default('0');
            $table->char('is_draft', 1)->default('1');
            $table->date('submitted_at')->nullable();
            $table->timestamps();
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
