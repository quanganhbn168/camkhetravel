<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bni_events', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 32)->default('handover')->index();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('kicker')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->foreignId('hero_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('video_url')->nullable();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable();
            $table->string('venue')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 32)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->boolean('is_featured')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('bni_chapters', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->nullable()->constrained('bni_events')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_name', 48)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('logo_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->foreignId('cover_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 32)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('bni_purposes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->constrained('bni_events')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon', 80)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('bni_schedule_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->constrained('bni_events')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_number')->default(1)->index();
            $table->time('starts_at')->nullable();
            $table->time('ends_at')->nullable();
            $table->string('title');
            $table->string('stage')->nullable();
            $table->text('description')->nullable();
            $table->text('result')->nullable();
            $table->string('location')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('bni_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->constrained('bni_events')->cascadeOnDelete();
            $table->string('type', 32)->default('handover')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('link_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bni_gallery_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->nullable()->constrained('bni_events')->nullOnDelete();
            $table->foreignId('bni_chapter_id')->nullable()->constrained('bni_chapters')->nullOnDelete();
            $table->string('group', 32)->default('event')->index();
            $table->string('title')->nullable();
            $table->string('caption')->nullable();
            $table->foreignId('media_id')->constrained('curator')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('bni_articles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->nullable()->constrained('bni_events')->nullOnDelete();
            $table->foreignId('bni_chapter_id')->nullable()->constrained('bni_chapters')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 32)->default('event')->index();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->foreignId('cover_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('status', 32)->default('draft')->index();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('bni_invitations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->constrained('bni_events')->cascadeOnDelete();
            $table->foreignId('bni_chapter_id')->nullable()->constrained('bni_chapters')->nullOnDelete();
            $table->string('guest_name');
            $table->string('slug')->unique();
            $table->string('company_name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone', 32)->nullable();
            $table->string('position')->nullable();
            $table->string('invitation_code', 32)->nullable()->unique();
            $table->string('rsvp_status', 32)->default('pending')->index();
            $table->unsignedTinyInteger('guest_count')->default(1);
            $table->text('rsvp_note')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bni_registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->constrained('bni_events')->cascadeOnDelete();
            $table->foreignId('bni_chapter_id')->nullable()->constrained('bni_chapters')->nullOnDelete();
            $table->foreignId('bni_invitation_id')->nullable()->constrained('bni_invitations')->nullOnDelete();
            $table->string('full_name');
            $table->string('email')->nullable()->index();
            $table->string('phone', 32)->nullable();
            $table->string('team_name')->nullable();
            $table->string('skill_level', 32)->nullable();
            $table->string('status', 32)->default('pending')->index();
            $table->text('note')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bni_reactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('reactable');
            $table->string('reaction', 32)->default('like');
            $table->timestamps();

            $table->unique(['user_id', 'reactable_type', 'reactable_id'], 'bni_reactions_unique_user_target');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('bni_chapter_id')->nullable()->after('id')->constrained('bni_chapters')->nullOnDelete();
        });

        Schema::table('comments', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('bni_chapter_id');
        });

        Schema::dropIfExists('bni_reactions');
        Schema::dropIfExists('bni_registrations');
        Schema::dropIfExists('bni_invitations');
        Schema::dropIfExists('bni_articles');
        Schema::dropIfExists('bni_gallery_items');
        Schema::dropIfExists('bni_activities');
        Schema::dropIfExists('bni_schedule_items');
        Schema::dropIfExists('bni_purposes');
        Schema::dropIfExists('bni_chapters');
        Schema::dropIfExists('bni_events');
    }
};
