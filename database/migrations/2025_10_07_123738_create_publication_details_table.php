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
        Schema::create('publication_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_of_extension_id')->constrained('work_of_extensions')->index('pub_details_pk');
            $table->string('publication_type', 50);
            $table->string('editorial')->nullable();
            $table->string('isbn_issn', 50)->nullable();
            $table->text('target_audience')->nullable();
            $table->string('language', 20)->default('español');
            $table->integer('print_run')->nullable();
            $table->text('relevance_justification');
            $table->date('publication_date');
            $table->string('media_type', 100);
            $table->string('media_nature', 100)->nullable();
            $table->timestamps();

            // $table->primary(['work_of_extension_id'], 'pub_details_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publication_details');
    }
};
