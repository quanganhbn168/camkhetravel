<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->unsignedBigInteger('linked_source_id')->nullable();
            $table->string('linked_source_type', 64)->nullable();
            $table->string('label');
            $table->text('url')->nullable();
            $table->string('target', 32)->nullable();
            $table->text('css_classes')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->index(['menu_id', 'parent_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
