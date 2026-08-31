<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bni_events', function (Blueprint $table): void {
            $table->foreignId('video_media_id')
                ->nullable()
                ->after('hero_media_id')
                ->constrained('curator')
                ->nullOnDelete();
            $table->foreignId('video_poster_media_id')
                ->nullable()
                ->after('video_media_id')
                ->constrained('curator')
                ->nullOnDelete();
            $table->string('registration_label')->nullable()->after('video_url');
            $table->string('registration_url', 2048)->nullable()->after('registration_label');
        });

        Schema::table('bni_chapters', function (Blueprint $table): void {
            $table->foreignId('video_media_id')
                ->nullable()
                ->after('cover_media_id')
                ->constrained('curator')
                ->nullOnDelete();
            $table->string('video_url', 2048)->nullable()->after('video_media_id');
        });
    }

    public function down(): void
    {
        Schema::table('bni_chapters', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('video_media_id');
            $table->dropColumn('video_url');
        });

        Schema::table('bni_events', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('video_media_id');
            $table->dropConstrainedForeignId('video_poster_media_id');
            $table->dropColumn(['registration_label', 'registration_url']);
        });
    }
};
