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
        Schema::create('certifications', function (Blueprint $table) {
            $table->id()->index('cert_id_idx');
            $table->foreignId('work_of_extension_id')->constrained('work_of_extensions');
            $table->string('certification_number', 50)->unique('cert_number_uq');
            $table->date('issue_date');
            $table->date('valid_until');
            $table->foreignId('issued_by_user_id')->constrained('users');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certifications');
        Schema::dropTable('sessions');
    }
};
