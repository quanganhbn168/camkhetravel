<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 64)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('view_name');
            $table->string('css_class');
            $table->string('css_source');
            $table->string('use_case')->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_path', 1024)->nullable();
            $table->string('icon')->nullable();
            $table->json('palette');
            $table->json('settings_schema')->nullable();
            $table->json('default_settings')->nullable();
            $table->json('default_sections')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::table('landings', function (Blueprint $table): void {
            $table->foreignId('landing_template_id')
                ->nullable()
                ->after('template_key')
                ->constrained('landing_templates')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('landings', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('landing_template_id');
        });

        Schema::dropIfExists('landing_templates');
    }
};
