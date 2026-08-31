<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bni_event_slides', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->constrained('bni_events')->cascadeOnDelete();
            $table->foreignId('media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('button_label')->nullable();
            $table->string('button_url', 2048)->nullable();
            $table->string('alt_text')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['bni_event_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bni_event_slides');
    }
};
