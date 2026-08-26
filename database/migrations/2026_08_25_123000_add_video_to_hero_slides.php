<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table): void {
            $table->string('video_source', 20)->nullable()->after('curator_media_id');
            $table->string('video_url', 1024)->nullable()->after('video_source');
            $table->foreignId('video_media_id')->nullable()->after('video_url')->constrained('curator')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('video_media_id');
            $table->dropColumn(['video_source', 'video_url']);
        });
    }
};
