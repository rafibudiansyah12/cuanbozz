<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('service_type', 20)->default('take_away')->after('table_number');
        });

        Schema::create('product_option_groups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('store_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('selection_type', 20)->default('single');
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('min_select')->default(0);
            $table->unsignedInteger('max_select')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['store_id', 'product_id', 'sort_order']);
        });

        Schema::create('product_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('store_id')->constrained()->restrictOnDelete();
            $table->foreignId('option_group_id')->constrained('product_option_groups')->cascadeOnDelete();
            $table->string('name', 100);
            $table->integer('price_delta')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['store_id', 'option_group_id', 'sort_order']);
        });

        Schema::create('order_item_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('store_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->string('group_name', 100);
            $table->string('option_name', 100);
            $table->integer('price_delta');
            $table->timestamps();
            $table->index(['store_id', 'order_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_options');
        Schema::dropIfExists('product_options');
        Schema::dropIfExists('product_option_groups');

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn('service_type');
        });
    }
};
