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
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key');
            $table->mediumText('value');
            $table->integer('expiration');

            $table->primary(['key'], 'cache_pk');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key');
            $table->string('owner');
            $table->integer('expiration');

            $table->primary(['key'], 'cache_lock_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
