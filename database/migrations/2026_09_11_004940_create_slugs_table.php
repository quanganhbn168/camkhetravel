<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slugs', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->morphs('sluggable');
            $table->timestamps();
            $table->unique(['sluggable_type', 'sluggable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slugs');
    }
};
