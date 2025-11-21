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
        Schema::create('work_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_of_extension_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recipient_user_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['work_of_extension_id', 'created_at']);
            $table->index(['sender_user_id', 'recipient_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_messages');
    }
};
