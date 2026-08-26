<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->string('faq_title')->nullable()->after('gallery');
            $table->text('faq_description')->nullable()->after('faq_title');
            $table->json('faq_items')->nullable()->after('faq_description');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropColumn(['faq_title', 'faq_description', 'faq_items']);
        });
    }
};
