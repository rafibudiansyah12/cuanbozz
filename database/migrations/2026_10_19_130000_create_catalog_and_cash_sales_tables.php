<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->restrictOnDelete();
            $table->string('name', 100);
            $table->timestamps();
            $table->unique(['store_id', 'name']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku', 100)->nullable();
            $table->unsignedInteger('price');
            $table->string('unit', 30)->default('pcs');
            $table->boolean('track_stock')->default(true);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['store_id', 'sku']);
            $table->index(['store_id', 'name']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->restrictOnDelete();
            $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
            $table->string('order_number', 40);
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('total');
            $table->unsignedBigInteger('cash_received');
            $table->unsignedBigInteger('change_amount');
            $table->string('payment_method', 20)->default('cash');
            $table->string('status', 20)->default('paid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->unique(['store_id', 'order_number']);
            $table->index(['store_id', 'created_at']);
            $table->index(['store_id', 'cashier_id', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('line_total');
            $table->string('note')->nullable();
            $table->timestamps();
            $table->index(['store_id', 'order_id']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('type', 24);
            $table->integer('quantity');
            $table->unsignedInteger('stock_after');
            $table->string('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['store_id', 'product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
