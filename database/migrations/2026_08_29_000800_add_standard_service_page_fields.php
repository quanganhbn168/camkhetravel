<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->foreignId('banner_video_media_id')
                ->nullable()
                ->after('curator_media_id')
                ->constrained('curator')
                ->nullOnDelete();
            $table->foreignId('process_background_media_id')
                ->nullable()
                ->after('process_items')
                ->constrained('curator')
                ->nullOnDelete();
            $table->string('projects_title')->nullable()->after('benefit_description');
            $table->foreignId('commitment_media_id')
                ->nullable()
                ->after('reference_videos')
                ->constrained('curator')
                ->nullOnDelete();
            $table->string('commitment_title')->nullable()->after('commitment_media_id');
            $table->text('commitment_description')->nullable()->after('commitment_title');
            $table->json('commitment_items')->nullable()->after('commitment_description');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('banner_video_media_id');
            $table->dropConstrainedForeignId('process_background_media_id');
            $table->dropConstrainedForeignId('commitment_media_id');
            $table->dropColumn([
                'projects_title',
                'commitment_title',
                'commitment_description',
                'commitment_items',
            ]);
        });
    }
};
