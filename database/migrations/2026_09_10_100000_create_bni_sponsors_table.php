<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bni_sponsors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->constrained('bni_events')->cascadeOnDelete();
            $table->string('tier', 32)->default('co_sponsor')->index();
            $table->string('name');
            $table->string('url', 2048)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['bni_event_id', 'tier', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bni_sponsors');
    }
};
