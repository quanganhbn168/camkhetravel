<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->string('process_title')->nullable()->after('faq_items');
            $table->text('process_description')->nullable()->after('process_title');
            $table->json('process_items')->nullable()->after('process_description');
            $table->string('benefit_title')->nullable()->after('process_items');
            $table->text('benefit_description')->nullable()->after('benefit_title');
            $table->json('benefit_items')->nullable()->after('benefit_description');
            $table->string('stats_title')->nullable()->after('benefit_items');
            $table->text('stats_description')->nullable()->after('stats_title');
            $table->json('stats_items')->nullable()->after('stats_description');
            $table->json('reference_videos')->nullable()->after('stats_items');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn([
                'process_title',
                'process_description',
                'process_items',
                'benefit_title',
                'benefit_description',
                'benefit_items',
                'stats_title',
                'stats_description',
                'stats_items',
                'reference_videos',
            ]);
        });
    }
};
