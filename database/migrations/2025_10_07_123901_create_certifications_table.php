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
            $table->id();
            $table->unsignedBigInteger('work_of_extension_id');
            $table->string('certification_number', 50)->unique('cert_number_uq');
            $table->date('issue_date');
            $table->date('valid_until');
            $table->unsignedBigInteger('issued_by_user_id');
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->primary(['id'], 'cert_pk');
            $table->index('work_of_extension_id', 'cert_work_idx');
            $table->index('issued_by_user_id', 'cert_issued_idx');
            $table->foreign('work_of_extension_id', 'cert_work_fk')
                ->references('id')
                ->on('work_of_extensions');
            $table->foreign('issued_by_user_id', 'cert_issued_fk')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
