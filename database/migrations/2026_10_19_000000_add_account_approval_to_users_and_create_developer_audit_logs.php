<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected', 'suspended'])
                ->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_developer')->default(false);
        });

        Schema::create('developer_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained('users')->restrictOnDelete();
            $table->enum('action', ['approve', 'reject', 'suspend', 'reactivate']);
            $table->foreignId('target_user_id')->constrained('users')->restrictOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developer_audit_logs');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'approval_status',
                'approved_at',
                'approved_by',
                'rejection_reason',
                'is_developer',
            ]);
        });
    }
};
