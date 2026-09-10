<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_team_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('about_department_id')->constrained('about_departments')->cascadeOnDelete();
            $table->foreignId('media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('name');
            $table->string('position')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['about_department_id', 'is_active', 'sort_order'], 'about_team_members_visibility_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_team_members');
    }
};
