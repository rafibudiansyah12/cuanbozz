<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('customer_name', 100)->nullable()->after('cashier_id');
            $table->string('table_number', 20)->nullable()->after('customer_name');
            $table->string('receipt_token', 64)->nullable()->unique()->after('table_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropUnique(['receipt_token']);
            $table->dropColumn(['customer_name', 'table_number', 'receipt_token']);
        });
    }
};
