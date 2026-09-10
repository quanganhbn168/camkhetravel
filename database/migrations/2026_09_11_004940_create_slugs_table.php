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
            $table->string('slug');
            $table->morphs('sluggable');
            $table->string('locale', 10)->default('vi');
            $table->timestamps();
            $table->unique(['slug', 'locale']);
            $table->unique(['sluggable_type', 'sluggable_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slugs');
    }
};
