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
        Schema::create('organizational_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 50);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            $table->primary(['id'], 'ou_pk');
            $table->index('parent_id', 'ou_parent_idx');
            $table->foreign('parent_id', 'ou_parent_fk')
                ->references('id')
                ->on('organizational_units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizational_units');
    }
};
