<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_members', function (Blueprint $table): void {
            $table->enum('role', ['owner', 'admin', 'cashier'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('store_members', function (Blueprint $table): void {
            $table->enum('role', ['owner', 'cashier'])->change();
        });
    }
};
