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
        Schema::table('work_of_extensions', function (Blueprint $table) {
            $table->boolean('publication_authorized')->default(false)->after('publication_consent');
            $table->timestamp('publication_authorized_at')->nullable()->after('publication_authorized');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_of_extensions', function (Blueprint $table) {
            $table->dropColumn(['publication_authorized', 'publication_authorized_at']);
        });
    }
};
