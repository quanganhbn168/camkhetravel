<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slide_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hero_slide_id')->constrained('hero_slides')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('eyebrow')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('primary_label')->nullable();
            $table->string('primary_url')->nullable();
            $table->string('secondary_label')->nullable();
            $table->string('secondary_url')->nullable();
            $table->timestamps();
            $table->unique(['hero_slide_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slide_translations');
    }
};
