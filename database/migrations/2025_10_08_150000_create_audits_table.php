<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table): void {
            $table->id()->index('audits_id_idx');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event', 100);
            $table->string('auditable_type', 150)->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('event', 'audits_event_idx');
            $table->index('created_at', 'audits_created_idx');
            $table->index(['auditable_type', 'auditable_id'], 'audits_audit_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
