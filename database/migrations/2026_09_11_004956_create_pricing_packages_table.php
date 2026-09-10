<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_packages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_pricing_id')->constrained('service_pricings')->cascadeOnDelete();
            $table->string('name');
            $table->string('badge')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('list_price')->nullable();
            $table->string('price_label')->nullable();
            $table->string('price_unit')->nullable();
            $table->string('promotion_type', 32)->nullable();
            $table->decimal('promotion_value', 12, 2)->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_packages');
    }
};
