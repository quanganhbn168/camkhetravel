<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landings', function (Blueprint $table): void {
            $table->string('layout_mode', 32)->default('standard')->index()->after('faq_items');
            $table->string('template_key', 64)->nullable()->after('layout_mode');
            $table->json('sections')->nullable()->after('template_key');
            $table->json('theme_settings')->nullable()->after('sections');
            $table->timestamp('campaign_starts_at')->nullable()->index()->after('theme_settings');
            $table->timestamp('campaign_ends_at')->nullable()->index()->after('campaign_starts_at');
            $table->string('expired_behavior', 32)->default('show_message')->after('campaign_ends_at');
            $table->text('expired_message')->nullable()->after('expired_behavior');
            $table->boolean('show_header')->default(true)->after('expired_message');
            $table->boolean('show_footer')->default(true)->after('show_header');
            $table->boolean('tracking_enabled')->default(true)->index()->after('show_footer');
        });

        Schema::create('landing_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('landing_id')->constrained()->cascadeOnDelete();
            $table->string('event_name', 64)->index();
            $table->string('block_id', 100)->nullable()->index();
            $table->string('visitor_id', 64)->nullable()->index();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('utm_source')->nullable()->index();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable()->index();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('gclid')->nullable();
            $table->string('fbclid')->nullable();
            $table->text('page_url')->nullable();
            $table->text('referrer')->nullable();
            $table->json('payload')->nullable();
            $table->char('ip_hash', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();

            $table->index(['landing_id', 'event_name', 'occurred_at'], 'landing_events_rollup_idx');
        });

        Schema::table('contact_requests', function (Blueprint $table): void {
            $table->string('landing_block_id', 100)->nullable()->after('landing_id');
            $table->string('visitor_id', 64)->nullable()->index()->after('landing_block_id');
            $table->string('session_id', 64)->nullable()->index()->after('visitor_id');
            $table->string('utm_source')->nullable()->index()->after('session_id');
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->string('utm_campaign')->nullable()->index()->after('utm_medium');
            $table->string('utm_content')->nullable()->after('utm_campaign');
            $table->string('utm_term')->nullable()->after('utm_content');
            $table->string('gclid')->nullable()->after('utm_term');
            $table->string('fbclid')->nullable()->after('gclid');
            $table->text('first_url')->nullable()->after('fbclid');
            $table->text('referrer')->nullable()->after('first_url');
        });
    }

    public function down(): void
    {
        Schema::table('contact_requests', function (Blueprint $table): void {
            $table->dropIndex(['visitor_id']);
            $table->dropIndex(['session_id']);
            $table->dropIndex(['utm_source']);
            $table->dropIndex(['utm_campaign']);
            $table->dropColumn([
                'landing_block_id',
                'visitor_id',
                'session_id',
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_content',
                'utm_term',
                'gclid',
                'fbclid',
                'first_url',
                'referrer',
            ]);
        });

        Schema::dropIfExists('landing_events');

        Schema::table('landings', function (Blueprint $table): void {
            $table->dropIndex(['layout_mode']);
            $table->dropIndex(['campaign_starts_at']);
            $table->dropIndex(['campaign_ends_at']);
            $table->dropIndex(['tracking_enabled']);
            $table->dropColumn([
                'layout_mode',
                'template_key',
                'sections',
                'theme_settings',
                'campaign_starts_at',
                'campaign_ends_at',
                'expired_behavior',
                'expired_message',
                'show_header',
                'show_footer',
                'tracking_enabled',
            ]);
        });
    }
};
