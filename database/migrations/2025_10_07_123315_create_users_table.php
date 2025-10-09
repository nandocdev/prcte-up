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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email', 100)->unique('users_email_uq');
            $table->string('password');
            $table->string('cedula', 20)->unique('users_cedula_uq');
            $table->string('professor_code', 20)->nullable()->unique('users_prof_code_uq');
            $table->unsignedBigInteger('main_organizational_unit_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->char('is_active', 1)->default('1');
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();

            $table->primary(['id'], 'usr_pk');
            $table->index('main_organizational_unit_id', 'usr_main_unit_idx');
            $table->foreign('main_organizational_unit_id', 'usr_main_unit_fk')
                ->references('id')
                ->on('organizational_units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
